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
        $content = (string) $request->getContent();

        if (
            in_array($request->header('X-GitHub-Event'), ['pull_request', 'push'])
            && $this->verifySignature($content, $project->secret, $request->header('X-Hub-Signature'))
        ) {
            $payload = $request->all();
            $ref = array_get($payload, 'ref', array_get($payload, 'pull_request.head.ref'));
            $commit = preg_replace('#^refs/heads/#', '', $ref);

            dispatch(new BuildProject($project, $commit));
        }
    }

    /**
     * @param string $content
     * @param string $secret
     * @param string $userSignature
     */
    protected function verifySignature(string $content, string $secret, string $userSignature)
    {
        $knownSignature = 'sha1=' . hash_hmac('sha1', $content, $secret);
        return hash_equals($knownSignature, $userSignature);
    }
}
