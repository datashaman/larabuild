<?php

namespace App\Jobs;

use App\Models\Build;
use App\Models\Project;
use Exception;
use File;
use GitWrapper\GitWrapper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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
    protected $title;

    /**
     * @var string
     */
    protected $ref;

    /**
     * @var string
     */
    protected $sha;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    public $timeout = 86400;

    /**
     * @param Project $project
     * @param string $title
     * @param string $ref
     * @param string $sha
     */
    public function __construct(
        Project $project,
        string $title,
        string $ref,
        string $sha
    ) {
        $this->project = $project;
        $this->title = $title;
        $this->ref = $ref;
        $this->sha = $sha;
        $this->password = Str::random(16);
    }

    /**
     * @param string $type
     * @param string $buffer
     *
     * @return string
     */
    protected function prefixBuffer(
        string $type,
        string $buffer
    ): string {
        return collect(preg_split('/\n/', $buffer))
            ->map(
                function ($line) use ($type) {
                    if (trim($line)) {
                        return date('H:i:s') . " [$type] " . $line;
                    }

                    return $line;
                }
            )
            ->implode("\n");
    }

    /**
     * @param string   $type
     * @param string   $buffer
     * @param resource $outputFile
     *
     * @return string
     */
    protected function processBuffer(
        string $type,
        string $buffer,
        $outputFile
    ): string {
        $buffer = $this->prefixBuffer($type, $buffer);
        fwrite($outputFile, $buffer);

        return $buffer;
    }

    /**
     * @param Build  $build
     * @param string $workspace
     * @param array  $cmd
     * @param resource|null $outputFile
     *
     * @return string
     */
    protected function createDockerComposeProcess(
        Build $build,
        string $workspace,
        array $cmd,
        $outputFile = null
    ): string {
        if (false && !is_resource($outputFile)) {
            throw new Exception('outputFile must be a resource');
        }

        $projectId = snake_case($build->project->id);

        $cmdLine = "/usr/local/bin/docker-compose --no-ansi -p {$projectId}_{$build->number} -f ". base_path('docker-compose.worker.yml') . ' ' . implode(' ', $cmd);
        Log::debug("Command Line", compact('cmdLine'));

        $composerCache = $build->project->getComposerCache();
        $npmCache = $build->project->getNpmCache();

        $env = [
            'COMPOSER_CACHE' => $composerCache,
            'DB_DATABASE' => "{$projectId}_testing",
            'DB_HOST' => 'db',
            'DB_USERNAME' => "{$projectId}_testing",
            'DB_PASSWORD' => $this->password,
            'HOME' => '/home/webapp',
            'NPM_CACHE' => $npmCache,
            'WORKSPACE' => $workspace,
        ];

        $process = Process::fromShellCommandLine(
            $cmdLine,
            '/tmp',
            $env
        );

        $process->setTimeout(86400);
        $process->setIdleTimeout(3600);
        $process->setPty(true);

        if ($outputFile) {
            $process->mustRun(
                function ($type, $buffer) use (&$output, $outputFile) {
                    if (is_resource($outputFile)) {
                        $this->processBuffer($type, $buffer, $outputFile);
                    }
                }
            );
        } else {
            $process->mustRun();
        }
    }

    /**
     * @param Build    $build
     * @param string   $workspace
     * @param resource $outputFile
     *
     * @return string
     */
    protected function dockerComposeUp(Build $build, string $workspace, $outputFile): string
    {
        return $this->createDockerComposeProcess($build, $workspace, ['up', '-d']);
    }

    /**
     * @param Build    $build
     * @param string   $workspace
     * @param resource $outputFile
     *
     * @return string
     */
    protected function dockerComposeRemove(Build $build, string $workspace, $outputFile): string
    {
        return '';
        return $this->createDockerComposeProcess($build, $workspace, ['rm', '--force', '--stop']);
    }

    /**
     * @param Build    $build
     * @param string   $workspace
     * @param string   $cmd
     * @param resource $outputFile
     *
     * @return string
     */
    protected function dockerComposeExec(
        Build $build,
        string $workspace,
        string $cmd,
        $outputFile
    ) {
        return $this->createDockerComposeProcess(
            $build,
            $workspace,
            ['exec', 'worker', $cmd],
            $outputFile
        );
    }

    /**
     * @param Build $build
     *
     * @return string
     */
    protected function createWorkspace(Build $build): string
    {
        $workspace = $build->getWorkspace();
        Log::debug("Working Dir", compact('workspace'));

        if (File::isDirectory($workspace)) {
            File::deleteDirectory($workspace);
        }

        File::makeDirectory($workspace, 0770, true, true);

        return $workspace;
    }

    /**
     * @param Build $build
     *
     * @return resource
     */
    protected function createOutputFile(Build $build)
    {
        $outputFile = $build->getOutputFile();
        $dir = dirname($outputFile);
        File::makeDirectory($dir, 0755, true, true);

        return fopen($outputFile, "a");
    }

    public function handle()
    {
        $wrapper = app(GitWrapper::class);

        $filename = tempnam(sys_get_temp_dir(), 'larabuild');

        file_put_contents($filename, $this->project->private_key);
        $wrapper->setPrivateKey($filename);
        unlink($filename);

        $build = $this->project->createBuild($this->sha);
        Log::debug("Build", compact('build'));

        $build->status = 'CHECKOUT';
        $build->save();

        $workspace = $this->createWorkspace($build);

        $cwd = getcwd();
        chdir($workspace);

        $repo = $wrapper->cloneRepository($this->project->repository, $workspace);
        $repo->checkout($this->sha);

        $filename = "$workspace/.larabuild.yml";

        if (!File::exists($filename)) {
            $build->status = 'NOT_FOUND';
            $build->save();

            return;
        }

        $projectId = snake_case($build->project->id);

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

        $outputFile = $this->createOutputFile($build);

        $this->dockerComposeUp($build, $workspace, $outputFile);

        collect($install)
            ->each(
                function ($cmd) use ($build, $workspace, $outputFile) {
                    $this->dockerComposeExec(
                        $build,
                        $workspace,
                        $cmd,
                        $outputFile
                    );
                }
            );

        $this->dockerComposeRemove($build, $workspace, $outputFile);

        fclose($outputFile);

        $build->output = file_get_contents($outputFile);

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
