<?php

namespace Luna;


use Luna\Exceptions\ResourceNotRegisteredException;
use Luna\Resources\Resource;
use Luna\Tools\Tool;
use Illuminate\Foundation\Application;

class Luna
{
    /** @var Application */
    protected $app;
    protected $resources = [];
    protected $tools = [];

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
        $resource = new $resource();
        $this->resources[(new \ReflectionClass($resource))->getShortName()] = $resource;

        if ($boot) {
            $resource->boot();
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

    function export()
    {
        return [
            'resources' => $this->exportResources(),
            'tools' => $this->exportTools(),
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