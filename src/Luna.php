<?php

namespace Luna;


use Luna\Exceptions\NotARegisterableResource;
use Luna\Exceptions\ResourceNotRegisteredException;
use Luna\Menu\Item;
use Luna\Resources\Resource;
use Luna\Tools\Tool;
use Illuminate\Foundation\Application;

class Luna
{
    /** @var Application */
    protected $app;
    protected $resources = [];
    protected $tools = [];
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
        $resource = is_object($resource) ? $resource : (new $resource());

        if ($resource instanceof Resource) {
            $this->resources[(new \ReflectionClass($resource))->getShortName()] = $resource;

            if ($boot) {
                $resource->boot();
            }
        } else {
            throw new NotARegisterableResource($resource);
        }
    }

    function getResources()
    {
        return $this->resources;
    }

    function getResource($name)
    {
        try {
            $reflection = new \ReflectionClass($name);
            $index = $reflection->getShortName();
        } catch (\ReflectionException $e) {
            $index = $name;
        }

        if (isset($this->resources[$index])) {
            return $this->resources[$index];
        }

        throw new ResourceNotRegisteredException($name);
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

    function exportMenu()
    {
        $menu = [];

        foreach ($this->menu as $item) {
            $menu[] = $item->export();
        }

        return $menu;
    }

    function exportIndexPage() {
        if (config('luna.index_page.type') == 'resource') {
            $name = (new \ReflectionClass($this->getResource(config('luna.index_page.resource'))))->getShortName();

            return [
                'type' => 'resource',
                'resource' => $name
            ];
        }

        return null;
    }

    function export()
    {
        return [
            'route_prefix' => config('luna.route_prefix'),
            'resources' => $this->exportResources(),
            'tools' => $this->exportTools(),
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
