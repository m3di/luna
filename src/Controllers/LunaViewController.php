<?php

namespace Luna\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Luna;
use Luna\Views\View;

class LunaViewController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function render(Request $request, $view)
    {
        /** @var View $view */
        $view = luna::getView($view);
        return $view->render();
    }
}
