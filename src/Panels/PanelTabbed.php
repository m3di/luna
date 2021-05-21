<?php

namespace Luna\Panels;

use Luna\Types\Type;

class PanelTabbed extends Panel
{
    protected $assignsSeparateSpace = true;
    protected $name;

    /** @var Type */
    protected $fields = [];

    function getType()
    {
        return 'tabbed';
    }

    function __construct($name=null)
    {
        $this->name = $name;
    }

    public static function make()
    {
        return new static();
    }

    function export()
    {
        return [
                'name' => $this->name,
            ] + parent::export();
    }
}
