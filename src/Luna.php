<?php

namespace Luna;


use Luna\Exceptions\NotRegistrableException;
use Luna\Exceptions\NotRegisteredException;
use Luna\Resources\Resource;
use Luna\Tools\Tool;
use Illuminate\Foundation\Application;
use Luna\Views\View;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class Luna
{
    /** @var Application */
    protected $app;
    protected $resources = [];
    protected $tools = [];
    /** @var View[] */
    protected $views = [];
    protected $menu = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    function setResources($resources)
    {
        $this->resources = [];

        foreach ($resources as $resource) {
            $this->addResource($resource, false);
        }
    }

    function addResource($resource, $boot = true)
    {
        if (!is_object($resource)) {
            $resource = new $resource();
        }

        if ($resource instanceof Resource) {
            $this->resources[$resource->getName()] = $resource;
            if ($boot) $resource->boot();
        } else throw new NotRegistrableException($resource);
    }

    function getResources()
    {
        return $this->resources;
    }

    function getResource($name)
    {
        try {
            $reflection = new \ReflectionClass($name);
            $name = $reflection->getShortName();
        } catch (\ReflectionException $e) {
        }

        if (isset($this->resources[$name])) {
            return $this->resources[$name];
        }

        throw new NotRegisteredException($name);
    }

    function bootResources()
    {
        foreach ($this->resources as $resource) {
            $resource->boot();
        }
    }

    function setTools($tools)
    {
        $this->tools = [];

        foreach ($tools as $tool) {
            $this->addTool($tool);
        }
    }

    function addTool(Tool $tool)
    {
        $this->tools[$tool->getName()] = $tool;
    }

    function getTools()
    {
        return $this->tools;
    }

    function getTool($name)
    {
        return $this->tools[$name];
    }

    function setViews($views)
    {
        $this->views = [];

        foreach ($views as $view) {
            $this->addView($view);
        }
    }

    function addView($view)
    {
        if (!is_object($view)) {
            $view = new $view();
        }

        if ($view instanceof View) {
            $this->views[$view->getName()] = $view;
        } else throw new NotRegistrableException($view);
    }

    function getViews()
    {
        return $this->views;
    }

    function getView($name)
    {
        try {
            $reflection = new \ReflectionClass($name);
            $name = $reflection->getShortName();
        } catch (\ReflectionException $e) {
        }

        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        throw new NotRegisteredException($name);
    }

    function setMenu($menu)
    {
        $this->menu = $menu;
    }

    function exportResources()
    {
        $resources = [];

        foreach ($this->getResources() as $name => $resource) {
            if ($resource->authorize(auth()->user())) {
                $resources[$name] = $resource->export();
            }
        }

        return $resources;
    }

    function exportTools()
    {
        $tools = [];

        foreach ($this->getTools() as $name => $tool) {
            if ($tool->authorize(auth()->user())) {
                $tools[$name] = $tool->export();
            }
        }

        return $tools;
    }

    function exportViews()
    {
        $views = [];

        foreach ($this->getViews() as $name => $view) {
            if ($view->authorize(auth()->user())) {
                $views[$view->getName()] = $view->export();
            }
        }

        return $views;
    }

    function exportMenu()
    {
        $menu = [];

        $items = is_callable($this->menu) ? call_user_func($this->menu) : $this->menu;

        foreach ($items as $item) {
            $menu[] = $item->export();
        }

        return $menu;
    }

    function exportIndexPage()
    {
        if (config('luna.index_page.type') == 'resource') {
            try {
                $name = config('luna.index_page.resource');

                return [
                    'type' => 'resource',
                    'resource' => $this->getResource(is_callable($name) ? call_user_func($name) : $name)->getName(),
                ];
            } catch (\ReflectionException $e) {
            }
        }

        return null;
    }

    function export()
    {
        return [
            'route_prefix' => config('luna.route_prefix'),
            'resources' => $this->exportResources(),
            'tools' => $this->exportTools(),
            'views' => $this->exportViews(),
            'menu' => $this->exportMenu(),
            'index' => $this->exportIndexPage(),
        ];
    }

    function tap($callable, ...$parameters)
    {
        return is_callable($callable) ? call_user_func_array($callable, $parameters) : $callable;
    }

    /**
     * call if callable
     * @param $value
     * @param ...$params
     * @return false|mixed
     */
    function cic($value, ...$params)
    {
        return is_callable($value) ? call_user_func_array($value, $params) : $value;
    }

    function helpTreeUrl($tree)
    {
        return $this->cic(config('luna.ui.external_help_url_generator', null), $tree);
    }

    function boot()
    {
        $this->registerResources();
        $this->bootResources();
        $this->registerTools();
        $this->registerViews();
        $this->registerMenu();
    }

    private function registerResources()
    {
        $mode = config('luna.resources.mode', 'auto');

        if (!in_array($mode, ['auto', 'manual'])) {
            throw new \Exception("Luna resource register mode [{$mode}] is not valid.");
        }

        if ($mode == 'auto') {
            $path = config('luna.resources.auto', app_path('luna/resources'));
            $this->setResources(array_keys(ClassMapGenerator::createMap($path)));
        }


        if ($mode == 'manual') {
            $this->setResources(config('luna.resources.manual', []));
        }
    }

    private function registerTools()
    {
        // $this->app['luna']->setTools($this->tools());
    }

    private function registerViews()
    {
        $mode = config('luna.views.mode', 'auto');

        if (!in_array($mode, ['auto', 'manual'])) {
            throw new \Exception("Luna views register mode [{$mode}] is not valid.");
        }

        if ($mode == 'auto') {
            $path = config('luna.views.auto', storage_path('luna/views'));
            $this->setViews(array_keys(ClassMapGenerator::createMap($path)));
        }

        if ($mode == 'manual') {
            $this->setViews(config('luna.views.manual', []));
        }
    }

    private function registerMenu()
    {
        $this->setMenu(config('luna.menu', [
            \Luna\Menu\AllResources::make('منابغ', 'fa fa-database'),
        ]));
    }
}
