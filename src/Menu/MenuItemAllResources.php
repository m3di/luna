<?php


namespace Luna\Menu;


use Luna;

class MenuItemAllResources extends MenuItemGroup
{
    function items($items)
    {
        return $this;
    }

    function getItems()
    {
        return array_map(function (Luna\Resources\Resource $resource) {
            return new MenuItemResource($resource);
        }, array_filter(Luna::getResources(), function (Luna\Resources\Resource $resource) {
            return $resource->isVisible();
        }));
    }
}
