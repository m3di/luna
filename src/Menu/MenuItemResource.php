<?php


namespace Luna\Menu;


use Luna\Resources\Resource;

class MenuItemResource extends MenuItem
{
    /**
     * @var Resource
     */
    protected $resource;

    function __construct($class)
    {
        $this->resource = is_object($class) ? $class : new $class();
    }

    function getItemTitle()
    {
        return $this->resource->getPlural();
    }

    function export()
    {
        return parent::export() + [
                'resource' => (new \ReflectionClass($this->resource))->getShortName(),
            ];
    }

    function getItemType()
    {
        return 'resource';
    }

    public static function make($class)
    {
        return new static($class);
    }
}
