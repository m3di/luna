<?php

namespace Luna\Middleware;

use Luna\Exceptions\NotRegisteredException;
use Luna\Resources\Resource;
use Closure;
use Illuminate\Http\Request;
use Luna\Types\View;

class AccessView
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $param = $request->route('luna_view');

        try {
            /** @var View $view */
            $view = app('luna')->getView($param);
        } catch (NotRegisteredException $e) {
            return abort(404);
        }

        if (!$view->authorize($request->user())) {
            return abort(403);
        }

        return $next($request);
    }
}