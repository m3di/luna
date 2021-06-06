<?php


namespace Luna\Menu;

use Luna;
use Luna\Resources\Resource;

class AllResources extends GroupSimple
{
    function getLinks()
    {
        return array_map(function (Resource $resource) {
            return new ResourceLink($resource);
        }, array_filter(array_values(Luna::getResources()), function (Resource $resource) {
            return $resource->isVisible();
        }));
    }
}
