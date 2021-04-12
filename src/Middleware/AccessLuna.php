<?php

namespace Luna\Middleware;


use Closure;

class AccessLuna
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (\Gate::has('AccessLuna') && !\Gate::check('AccessLuna')) {
            abort(403);
        }

        return $next($request);
    }
}