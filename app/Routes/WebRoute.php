<?php

namespace App\Routes;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class WebRoute
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
    */
    protected $namespace = 'App\Controller';
    public function register(RouteCollection $routeCollection){
        $routeCollection->add('blog_index' , new Route('blog' , [
            '_controller'=> $this->namespace .'\HomeController'
        ]));
        return $routeCollection ;
    }
}
