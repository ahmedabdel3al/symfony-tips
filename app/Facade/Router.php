<?php

namespace App\Facade;

use App\Routing\Router as Route;

class Router extends Facade 
{ 
    public static function getFacadeAccessor(){
        
        return Route::class;
    }
}