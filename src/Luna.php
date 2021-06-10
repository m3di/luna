<?php

namespace Luna;


use Luna\Exceptions\NotRegistrableException;
use Luna\Exceptions\NotRegisteredException;
use Luna\Menu\Item;
use Luna\Resources\Resource;
use Luna\Tools\Tool;
use Illuminate\Foundation\Application;
use Luna\Views\View;

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
        $this->menu = [];

        foreach ($menu as $item) {
            $this->addMenu($item);
        }
    }

    function addMenu(Item $item, $index = null)
    {
        if (is_null($index)) {
            $this->menu[] = $item;
        } else {
            $this->menu[$index] = $item;
        }
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

        foreach ($this->menu as $item) {
            $menu[] = $item->export();
        }

        return $menu;
    }

    function exportIndexPage()
    {
        if (config('luna.index_page.type') == 'resource') {
            try {
                $name = $this->getResource(config('luna.index_page.resource'))->getName();

                return [
                    'type' => 'resource',
                    'resource' => $name
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

    function boot()
    {
        $this->bootResources();
    }

    function tap($callable, ...$parameters)
    {
        return is_callable($callable) ? call_user_func_array($callable, $parameters) : $callable;
    }
}
