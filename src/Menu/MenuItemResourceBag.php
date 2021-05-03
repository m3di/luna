<?php


namespace Luna\Menu;


use Luna\Resources\Resource;

class MenuItemResourceBag extends MenuItem
{
    protected $title;

    /**
     * @var Resource[]
     */
    protected $resources;

    function __construct($title)
    {
        $this->title = $title;
    }

    function getItemTitle()
    {
        return $this->title;
    }

    function export()
    {
        return parent::export() + [
                'items' => array_map(function($item){return (new MenuItemResource($item))->export();}, $this->resources),
            ];
    }

    function getItemType()
    {
        return 'resource-bag';
    }

    public static function make($title)
    {
        return new static($title);
    }

    function resources($resources) {
        $this->resources = is_callable($resources) ? call_user_func($resources) : $resources;
        return $this;
    }
}
