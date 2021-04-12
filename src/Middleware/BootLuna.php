<?php

namespace Luna\Middleware;


use Closure;

class BootLuna
{
    public function handle($request, Closure $next, ...$guards)
    {
        app('luna')->boot();

        return $next($request);
    }
}