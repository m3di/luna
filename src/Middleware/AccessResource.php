<?php

namespace Luna\Middleware;

use Luna\Exceptions\NotRegisteredException;
use Luna\Resources\Resource;
use Closure;
use Illuminate\Http\Request;

class AccessResource
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $param = $request->route('luna_resource');

        try {
            /** @var Resource $resource */
            $resource = app('luna')->getResource($param);
        } catch (NotRegisteredException $e) {
            return abort(404);
        }

        if (!$resource->authorize($request->user())) {
            return abort(403);
        }

        return $next($request);
    }
}