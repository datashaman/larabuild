<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\Process\Process;

class ConsoleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Team $team
     * @param Project $project
     * @param Build $build
     *
     * @return Response
     */
    public function __invoke(Request $request, Team $team, Project $project, string $number)
    {
        $build = $project->builds()
            ->where('number', $number)
            ->firstOrFail();

        $outputFile = $build->getOutputFile();

        return response()
            ->stream(function () use ($build, $outputFile) {
                if ($build->status === 'BUILDING') {
                    $process = new Process(['tail', '-f', $outputFile]);

                    $process->run(
                        function ($type, $buffer) use ($build, $process) {
                            echo $buffer;
                            flush();

                            $build->refresh();

                            if ($build->status !== 'BUILDING') {
                                $process->kill(SIGKILL);
                            }
                        }
                    );
                } else {
                    echo file_get_contents($outputFile);
                }
            }, 200, ['Content-Type' => 'text/plain']);
    }
}
