<?php

namespace Luna\Types;


class Number extends Type
{
    protected $type = 'number';

    protected $inheritedRules = ['numeric'];

    /**
     * @return static
     */
    static function make($name, $column_name = null)
    {
        return (new static($name))->columnName(is_null($column_name) ? $name : $column_name);
    }
}