<?php

namespace Luna\Facades;


use Illuminate\Support\Facades\Facade;

class Luna extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'luna';
    }
}