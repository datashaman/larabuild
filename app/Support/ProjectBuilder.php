<?php

namespace App\Support;

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
use Illuminate\Support\Carbon;
use Log;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProjectBuilder
{
    public function build(Project $project, string $commit)
    {
        $build = $project->createBuild($commit);

        $wrapper = app(GitWrapper::class);

        $filename = tempnam(sys_get_temp_dir(), 'larabuild');
        file_put_contents($filename, $build->project->private_key);
        $wrapper->setPrivateKey($filename);
        unlink($filename);

        $workingFolder = storage_path('app/workspace/' . $project->id);

        if (File::isDirectory($workingFolder)) {
            File::deleteDirectory($workingFolder);
        }

        $git = $wrapper->cloneRepository($build->project->repository, $workingFolder);

        $this->handle($build, $workingFolder);

        return $build;
    }

    /**
     * @param Build  $build
     * @param string $workingFolder
     */
    protected function handle(Build $build, string $workingFolder)
    {
        $output = '';

        $filename = "$workingFolder/.larabuild.yml";

        if (!File::exists($filename)) {
            $build->status = 'not-found';
            $build->save();

            return;
        }

        $config = Yaml::parseFile($filename);

        $install = array_get($config, 'install');

        if (!is_array($install)) {
            $install = [$install];
        }

        $build->status = 'started';

        $docker = (bool) array_get($config, 'docker', false);

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
                                Log::debug('Stdout', compact('buffer'));
                                $output .= $buffer;
                            }
                        );

                        $stream->onStderr(
                            function ($buffer) use (&$output) {
                                Log::debug('Stderr', compact('buffer'));
                                $output .= $buffer;

                                $build->status = 'fail';
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

            collect($install)
                ->each(
                    function ($command) use ($build, &$output) {
                        $process = new Process($command);

                        $process->run(
                            function ($type, $buffer) use (&$output) {
                                $output .= $buffer;
                            }
                        );

                        if (!$process->isSuccessful()) {
                            $build->status = 'fail';
                            return false;
                        }
                    }
                );

            chdir($cwd);
        }

        $build->output = $output;

        if ($build->status !== 'fail') {
            $build->status = 'success';
        }

        $build->completed_at = Carbon::now();

        if (!$build->save()) {
            Log::error('Error saving build', ['errors' => $build->errors()->all()]);
        }
    }
}
