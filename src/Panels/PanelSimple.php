<?php

namespace Luna\Panels;


class PanelSimple extends Panel
{
    protected $name;
    protected $support;

    function getType()
    {
        return 'simple';
    }

    function __construct($name = null, $support = true)
    {
        $this->name = $name;
        $this->support = $support;
    }

    public static function make($name = null, $support = true)
    {
        return new static($name, $support);
    }

    function export()
    {
        return [
                'name' => $this->name,
                'support' => $this->support,
            ] + parent::export();
    }
}
