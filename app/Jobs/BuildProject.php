<?php

namespace App\Jobs;

use App\Models\Build;
use App\Models\Project;
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

    /**
     * @param string $type
     * @param string $buffer
     * @param resource $outputFile
     */
    public function processBuffer($type, $buffer, $outputFile)
    {
        $buffer = collect(preg_split('/\n/', $buffer))
            ->map(
                function ($line) use ($type) {
                    if (trim($line)) {
                        return date('H:i:s') . " [$type] " . $line;
                    }

                    return $line;
                }
            )
            ->implode("\r\n");

        fwrite($outputFile, $buffer);

        return $buffer;
    }

    public function handle()
    {
        $wrapper = app(GitWrapper::class);

        $filename = tempnam(sys_get_temp_dir(), 'larabuild');

        file_put_contents($filename, $this->project->private_key);
        $wrapper->setPrivateKey($filename);
        unlink($filename);

        $build = $this->project->createBuild($this->commit);
        Log::debug("Build", compact('build'));

        $workingFolder = $build->getWorkingFolder();
        Log::debug("Working Folder", compact('workingFolder'));

        $build->status = 'CHECKOUT';
        $build->save();

        if (File::isDirectory($workingFolder)) {
            File::deleteDirectory($workingFolder);
        }

        File::makeDirectory($workingFolder, 0770, true, true);

        $cwd = getcwd();
        chdir($workingFolder);

        $repo = $wrapper->cloneRepository($this->project->repository, $workingFolder);
        $repo->checkout($this->commit);

        $filename = "$workingFolder/.larabuild.yml";

        if (!File::exists($filename)) {
            $build->status = 'NOT_FOUND';
            $build->save();

            return;
        }

        $composerCache = $build->project->getComposerCache();
        $npmCache = $build->project->getNpmCache();

        File::makeDirectory($composerCache, 0755, true, true);
        File::makeDirectory($npmCache, 0755, true, true);

        $config = Yaml::parseFile($filename);

        $install = Arr::get($config, 'install');

        if (!is_array($install)) {
            $install = [$install];
        }

        $build->status = 'BUILDING';
        $build->save();

        $output = '';

        $docker = (bool) Arr::get($config, 'docker', false);

        if (config('larabuild.docker') && $docker) {
            Log::debug('Docker build');

            $client = app(Docker::class);

            $hostConfig = app(HostConfig::class);

            $hostConfig->setBinds(
                [
                    "$composerCache:/home/webapp/.composer/cache",
                    "$npmCache:/home/webapp/.npm",
                    "$workingFolder:/workspace",
                ]
            );

            $containerConfig = app(ContainersCreatePostBody::class);
            $containerConfig->setHostConfig($hostConfig);
            $containerConfig->setImage('datashaman/larabuild-worker:latest');
            $containerConfig->setCmd(['bash']);
            $containerConfig->setTty(true);

            $containerCreateResult = $client->containerCreate($containerConfig);
            $containerId = $containerCreateResult->getId();

            $client->containerStart($containerId);

            $outputFile = $build->getOutputFile();
            $dir = dirname($outputFile);
            File::makeDirectory($dir, 0755, true, true);

            $outputFile = fopen($outputFile, "a");

            collect($install)
                ->each(
                    function ($command) use ($build, $client, $containerId, &$output, $outputFile) {
                        $execConfig = app(ContainersIdExecPostBody::class);
                        $execConfig->setAttachStderr(true);
                        $execConfig->setAttachStdout(true);
                        $execConfig->setWorkingDir('/workspace');
                        $execConfig->setCmd($command);

                        $execId = $client->containerExec($containerId, $execConfig)->getId();

                        $execStartConfig = app(ExecIdStartPostBody::class);
                        $execStartConfig->setDetach(false);

                        $stream = $client->execStart($execId, $execStartConfig);

                        $stream->onStdout(
                            function ($buffer) use (&$output, $outputFile) {
                                $output .= $this->processBuffer('out', $buffer, $outputFile);
                            }
                        );

                        $stream->onStderr(
                            function ($buffer) use (&$output, $outputFile) {
                                $output .= $this->processBuffer('err', $buffer, $outputFile);
                            }
                        );

                        $stream->wait();
                    }
                );

            fclose($outputFile);

            $client->containerKill($containerId, [
                'signal' => 'SIGKILL',
            ]);
        } else {
            Log::debug('Local build');

            $cwd = getcwd();
            chdir($workingFolder);

            $outputFile = $build->getOutputFile();

            $dir = dirname($outputFile);
            File::makeDirectory($dir, 0755, true, true);

            $outputFile = fopen($outputFile, "a");

            collect($install)
                ->each(
                    function ($command) use ($build, &$output, $outputFile) {
                        Log::debug('Executing command', compact('command'));

                        $process = app(Process::class, $command);
                        $process->setTimeout($this->project->timeout);

                        try {
                            $process->mustRun(
                                function ($type, $buffer) use (&$output, $outputFile) {
                                    $output .= $this->processBuffer($type, $buffer, $outputFile);
                                }
                            );
                        } catch (ProcessFailedException $exception) {
                            $build->status = 'FAILED';
                            return false;
                        }
                    }
                );

            fclose($outputFile);
        }

        $build->output = $output;

        if ($build->status !== 'FAILED') {
            $build->status = 'OK';
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
