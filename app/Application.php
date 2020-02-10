<?php

namespace App;

use Exception;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use App\Traits\ResovleMethodArguments;
use App\Traits\ResolveController;
use Closure;
use ReflectionFunction;

class Application implements HttpKernelInterface
{
    use ResovleMethodArguments , ResolveController;

    public  $dependancies = [];

    /**
     * handle incomming request
     *
     * @param Request $request
     * @param integer $type
     * @param boolean $catch
     * @return Response
     */
    public function handle(Request $request, int $type = self::MASTER_REQUEST, bool $catch = true)
    {
       
        $this->registerSingletones($request);
        $this->registerServiceProvider($this);
        //dispatch controller with method 
        return $this->resolveIncomingRequest($request);
    }
    /**
     * Set Singleton in Application 
     *
     * @param Request $request
     * @return void
     */
    protected function registerSingletones(Request $request){
        // register singleton of routeCollection 
        $this->singleTon(RouteCollection::class, function () {
            return new RouteCollection;
        });
        //register singleton of Request 
        $this->singleTon(Request::class , function() use ($request){
            return $request;
        });

    }
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param RouteCollection $router
     * @return void
     */
    public function resolveIncomingRequest($request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->get(RouteCollection::class), $context);

        $request->attributes->add($matcher->match($request->getPathInfo()));

        [$controller , $method] = $this->resolveController($request);
    
        if($controller instanceof Closure){
            $arguments = $this->resovleMethodArguments($request, [$controller , $method]);
            return $controller(...$arguments);
            
        }

        if(is_null($method)){
            return $this->get($controller)();
        }
        $arguments = $this->resovleMethodArguments($request, [$controller , $method]);

        
        return $this->get($controller)->{$method}(...$arguments);

    
    }
    public function registerServiceProvider($application)
    {
        $providers = require_once __DIR__ . '/../app/Provider/provider.php';
        foreach ($providers as $provider) {
            (new $provider($this))->map();
        }
    }
    /**
     * set item inside container
     *
     * @param [type] $name
     * @param callable $closure
     * @return void
     */
    public function set($name, callable $closure)
    {
        $this->dependancies[$name] = $closure;
    }
    /**
     * set item inside container if not resolved before
     *
     * @param [type] $name
     * @param callable $closure
     * @return object
     */
    public function singleTon($name, callable $closure)
    {
        $this->dependancies[$name] = function () use ($closure) {
            static $resolved;
            if (!$resolved) {
                $resolved = $closure($this);
            }
            return $resolved;
        };
    }
    /**
     * Check if Container Has dependance name
     *
     * @param [type] $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->dependancies[$name]);
    }
    /**
     * Resolve dependances
     *
     * @param [type] $name
     * @return void
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->dependancies[$name]($this);
        }
        return  $this->autowire($name);
    }
    public function autowire($name)
    {

        if (!class_exists($name)) {
            throw new Exception;
        }

        $reflector = $this->getReflector($name);

        if (!$reflector->isInstantiable()) {
            throw new Exception;
        }

        if ($constactor = $reflector->getConstructor()) {
            $dependances = $this->getConstructorDependances($constactor);
            return  $reflector->newInstanceArgs($dependances);
        }
        return new $name();
    }

    protected function getConstructorDependances($constactor)
    {
        return array_map(function ($dependacy) {
            return  $this->resolve($dependacy);
        }, $constactor->getParameters());
    }

    protected function resolve($dependacy)
    {
        if (is_null($dependacy->getClass())) {
            throw new Exception;
        }
        return  $this->get($dependacy->getClass()->getName());
    }
    /**
     *  Resolve dependances By Calling get Method
     *
     * @param [type] $name
     * @return void
     */
    public function __get($name)
    {
        return $this->get($name);
    }
    public function getReflector($class)
    {
        return new \ReflectionClass($class);
    }
}
