<?php

namespace App\Routing;

use App\Application;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Closure;

class Router
{

    public $routeCollection = [];
    public $namespace;

    public function name(String $name)
    {
        $this->routeCollection[key($this->routeCollection)] = array_merge(end($this->routeCollection), ['name' => $name]);
        return $this;
    }
    public function get($url, $action)
    {

        $this->routeCollection[] = ['action' => $this->parseAction($action), 'methods' => ['GET', 'HEAD'], 'url' => $url];
        return $this;
    }
    public function post($url, $action)
    {
        $this->routeCollection[] = ['action' => $this->parseAction($action), 'methods' => ['POST'], 'url' => $url];
        return $this;
    }
    public function delete()
    { }
    public function put()
    { }
    public function patch()
    { }
    public function any()
    { }
    public function match()
    { }

    public function parseAction($action)
    {
        if (!$action instanceof Closure) {

            return $this->namespace . rtrim($action, '/');
        }
        return $action;
    }
    /**
     * Set Name Space
     *
     * @param [type] $namespace
     * @return Router
     */
    public function namespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
    /**
     * Load Group inside RouteCollection
     *
     * @param [type] $path
     * @return Router
     */
    public function load($path)
    {
        require_once($path);
        return $this;
    }
    /**
     * Set Container inside Routing
     *
     * @param Application $application
     * @return void
     */
    public function container(Application $application)
    {
        $this->application = $application;
        return $this;
    }
    public function register()
    {
        $routes = $this->application->get(RouteCollection::class);

        foreach ($this->routeCollection as $route) {
            $routes->add(
                isset($route['name']) ? $route['name'] : $route['url'],
                new Route(
                    $route['url'],
                    [
                        '_controller' => $route['action']
                    ],
                    isset($route['wheres']) ? $route['wheres'] : [],
                    [],
                    null,
                    null,
                    $route['methods']
                )
            );
        }
    }
}
