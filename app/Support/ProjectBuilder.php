<?php

namespace App\Support;

use App\Models\Build;
use App\Models\Project;
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

        $output = '';

        $build->status = 'started';

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

        $build->output = $output;

        if ($build->status !== 'fail') {
            $build->status = 'success';
        }

        $build->completed_at = Carbon::now();

        if (!$build->save()) {
            Log::error('Error saving build', ['errors' => $build->errors()->all()]);
        }

        chdir($cwd);
    }
}
