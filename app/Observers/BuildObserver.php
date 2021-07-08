<?php

namespace App\Observers;

use App\Events\BuildUpdated;
use App\Models\Build;
use GuzzleHttp\Client;
use Log;

class BuildObserver
{
    /**
     * @var array
     */
    protected $states = [
        'NEW' => 'pending',
        'CHECKOUT' => 'pending',
        'BUILDING' => 'pending',
        'FAILED' => 'failure',
        'OK' => 'success',
        'NOT_FOUND' => 'error',
    ];

    /**
     * @var array
     */
    protected $descriptions = [
        'error' => 'The build has an error',
        'failure' => 'The build failed',
        'pending' => 'The build is pending',
        'success' => 'The build succeeded',
    ];

    /**
     * Handle the build "saved" event.
     *
     * @param  \App\Models\Build  $build
     * @return void
     */
    public function saved(Build $build)
    {
        event(new BuildUpdated($build));

        if ($build->isDirty('status')) {
            Log::debug("Build status is dirty", compact('build'));

            $state = $this->states[$build->status];
            $target_url = url("/#/{$build->project->team->id}/{$build->project->id}/{$build->number}");
            $description = $this->descriptions[$state];
            $context = 'ci/larabuild';

            $url = "https://api.github.com/repos/{$build->project->ownerRepo}/statuses/{$build->commit}";
            $json = compact('state', 'target_url', 'description', 'context');

            Log::debug(
                "GitHub status update",
                compact('url', 'json')
            );

            app(Client::class)->post($url, compact('json'));
        }
    }
}
