<?php

namespace Luna\Exceptions;


class ResourceNotRegisteredException extends \Exception
{
    protected $class_name;

    /**
     * ResourceNotRegisteredException constructor.
     * @param $class_name
     */
    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }


}