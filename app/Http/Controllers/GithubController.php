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
        $event = $request->header('X-GitHub-Event');

        if (!in_array($event, ['pull_request', 'push'])) {
            return;
        }

        switch ($event) {
        case 'pull_request':
            $action = $request->get('action');

            if (!in_array($action, ['opened', 'reopened'])) {
                return;
            }

            $title = $request->get('pull_request.title');
            $ref = $request->get('pull_request.head.ref');
            $sha = $request->get('pull_request.head.sha');
            break;

        case 'push':
            if ($request->get('deleted')) {
                return;
            }

            $title = $request->get('head_commit.message');
            $ref = $request->get('ref');
            $sha = $request->get('after');
            break;
        }

        dispatch(new BuildProject($project, $title, $ref, $sha));
    }
}
