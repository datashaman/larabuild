<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Str;

class ProjectObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param Project $project
     */
    public function creating(Project $project)
    {
        $project->secret = Str::random(32);
    }
}
