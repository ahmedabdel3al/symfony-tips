<?php

namespace App\Facade;

abstract class Facade
{

    public static function __callStatic($method, $arguments)
    {
        $class = static::getFacadeAccessor();
        $instance = new $class;
        return $instance->{$method}(...$arguments);
    }
}
