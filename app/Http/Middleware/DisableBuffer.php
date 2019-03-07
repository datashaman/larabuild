<?php

namespace App\Http\Middleware;

use Closure;

class DisableBuffer
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
        header("Cache-Control: no-cache, must-revalidate");
        header('X-Accel-Buffering: no');

        while(ob_get_level()) {
            ob_end_clean();
        }

        return $next($request);
    }
}
