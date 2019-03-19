<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class VerifyGithubSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $project = $request->route('project');

        if (!$project) {
            throw new Exception('Cannot verify signature without project');
        }

        $content = (string) $request->getContent();

        if (
            $this->verifySignature(
                $content,
                $project->secret,
                $request->header('X-Hub-Signature')
            )
        ) {
            return $next($request);
        }

        abort(404);
    }

    /**
     * @param string $content
     * @param string $secret
     * @param string $requestSignature
     */
    protected function verifySignature(string $content, string $secret, string $requestSignature)
    {
        $knownSignature = 'sha1=' . hash_hmac('sha1', $content, $secret);
        return hash_equals($knownSignature, $requestSignature);
    }
}
