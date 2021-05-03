<?php

namespace Luna\Exceptions;


class NotARegisterableResource extends \Exception
{
    protected $class_name;

    /**
     * NotARegisterableResource constructor.
     * @param $class_name
     */
    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }


}