<?php

namespace App\Lighthouse;

use GraphQL\Error\Error;
use Log;

class ErrorHandler implements \Nuwave\Lighthouse\Execution\ErrorHandler
{
    public static function handle(Error $error, \Closure $next): array
    {
        Log::error("GraphQL Error", compact('error'));

        return $next($error);
    }
}
