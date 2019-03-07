<?php

namespace App\Observers;

use App\Events\BuildUpdated;
use App\Models\Build;

class BuildObserver
{
    /**
     * Handle the build "saved" event.
     *
     * @param  \App\Models\Build  $build
     * @return void
     */
    public function saved(Build $build)
    {
        event(new BuildUpdated($build));
    }
}
