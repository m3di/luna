<?php

namespace Luna\Middleware;


use Closure;

class BootLuna
{
    public function handle($request, Closure $next, ...$guards)
    {
        app('luna')->internalBoot();

        return $next($request);
    }
}