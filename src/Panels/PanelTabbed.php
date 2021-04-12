<?php

namespace Luna\Panels;

use Luna\Types\Type;

class PanelTabbed extends Panel
{
    protected $assignsSeparateSpace = true;

    /** @var Type */
    protected $fields = [];

    function getType()
    {
        return 'tabbed';
    }

    function __construct()
    {
    }

    public static function make()
    {
        return new static();
    }
}
