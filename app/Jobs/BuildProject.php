<?php

namespace App\Jobs;

use App\Models\Build;
use App\Models\Project;
use Docker\API\Model\ContainerExec;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\API\Model\HostConfig;
use Docker\API\Model\Mount;
use Docker\Docker;
use File;
use GitWrapper\GitWrapper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Log;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BuildProject implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var string
     */
    protected $commit;

    /**
     * @param Project $project
     * @param string $commit
     */
    public function __construct(Project $project, string $commit)
    {
        $this->project = $project;
        $this->commit = $commit;
    }

    public function handle()
    {
        $wrapper = app(GitWrapper::class);

        $filename = tempnam(sys_get_temp_dir(), 'larabuild');

        file_put_contents($filename, $this->project->private_key);
        $wrapper->setPrivateKey($filename);
        unlink($filename);

        $build = $this->project->createBuild($this->commit);

        $workingFolder = $build->getWorkingFolder();
        Log::debug("Working Folder", compact('workingFolder'));

        $cwd = getcwd();

        if (File::isDirectory($workingFolder)) {
            chdir($workingFolder);
            $repo = $wrapper->workingCopy($workingFolder);
        } else {
            File::makeDirectory($workingFolder, 0770, true, true);
            chdir($workingFolder);
            $repo = $wrapper->cloneRepository($this->project->repository, $workingFolder);
        }

        $repo->reset(['hard' => true]);
        $repo->clean(['force' => true, 'd' => true]);
        $repo->checkout($this->commit);

        $filename = "$workingFolder/.larabuild.yml";

        if (!File::exists($filename)) {
            $build->status = 'NOT_FOUND';
            $build->save();

            return;
        }

        $config = Yaml::parseFile($filename);

        $install = Arr::get($config, 'install');

        if (!is_array($install)) {
            $install = [$install];
        }

        $build->status = 'STARTED';
        $build->save();

        $output = '';

        $docker = (bool) Arr::get($config, 'docker', false);

        if (config('larabuild.docker') && $docker) {
            Log::debug('Docker build');

            $client = app(Docker::class);

            $hostConfig = new HostConfig();
            $hostConfig->setBinds(["$workingFolder:/app"]);

            $containerConfig = new ContainersCreatePostBody();
            $containerConfig->setHostConfig($hostConfig);
            $containerConfig->setImage('ubuntu:latest');
            $containerConfig->setCmd(['bash']);
            $containerConfig->setTty(true);

            $containerCreateResult = $client->containerCreate($containerConfig);
            $containerId = $containerCreateResult->getId();

            $client->containerStart($containerId);

            collect($install)
                ->each(
                    function ($command) use ($build, $client, $containerId, &$output) {
                        $execConfig = new ContainersIdExecPostBody();
                        $execConfig->setAttachStderr(true);
                        $execConfig->setAttachStdout(true);
                        $execConfig->setWorkingDir('/app');
                        $execConfig->setCmd($command);

                        $execId = $client->containerExec($containerId, $execConfig)->getId();

                        $execStartConfig = new ExecIdStartPostBody();
                        $execStartConfig->setDetach(false);

                        $stream = $client->execStart($execId, $execStartConfig);

                        $stream->onStdout(
                            function ($buffer) use (&$output) {
                                $output .= date('H:i:s') . ' ' . $buffer;
                            }
                        );

                        $stream->onStderr(
                            function ($buffer) use (&$output) {
                                Log::debug('Stderr', compact('buffer'));
                                $output .= $buffer;

                                $build->status = 'FAIL';
                                $build->save();
                            }
                        );

                        $stream->wait();
                    }
                );

            $client->containerStop($containerId);
        } else {
            Log::debug('Local build');

            $cwd = getcwd();
            chdir($workingFolder);

            $outputFile = fopen("$workingFolder/output.txt", "a");

            collect($install)
                ->each(
                    function ($command) use ($build, &$output, $outputFile) {
                        Log::debug('Executing command', compact('command'));

                        $process = new Process($command);
                        $process->setTimeout($this->project->timeout);

                        try {
                            $process->mustRun(
                                function ($type, $buffer) use (&$output, $outputFile) {
                                    $buffer = date('H:i:s') . ' ' . $buffer;
                                    fwrite($outputFile, $buffer);
                                    $output .= $buffer;
                                }
                            );
                        } catch (ProcessFailedException $exception) {
                            $build->status = 'FAIL';
                            return false;
                        }
                    }
                );

            fclose($outputFile);

            chdir($cwd);
        }

        $build->output = $output;

        if ($build->status !== 'FAIL') {
            $build->status = 'SUCCESS';
        }

        $build->completed_at = Carbon::now();

        if ($build->save()) {
            Log::info('Build saved');
        } else {
            Log::error('Error saving build', ['errors' => $build->errors()->all()]);
        }

        chdir($cwd);
    }
}
