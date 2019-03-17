<?php

namespace App\Http\Controllers;

use App\Jobs\BuildProject;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

class GithubController extends Controller
{
    /**
     * @param Request $request
     * @param Project $project
     *
     * @return Response
     */
    public function __invoke(Request $request, Project $project)
    {
        $payload = $request->all();
        $ref = array_get($payload, 'ref', array_get($payload, 'pull_request.head.ref'));
        $commit = preg_replace('#^refs/heads/#', '', $ref);

        dispatch(new BuildProject($project, $commit));
    }
}
