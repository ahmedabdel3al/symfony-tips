<?php

namespace App;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Traits\ApplicationTraits;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Application implements HttpKernelInterface
{


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
        $this->singleTon('router', function () {
            return new RouteCollection;
        });
        $this->registerServiceProvider($this);
        //resolve controller 
        return $this->resolveIncomingRequest($request, $this->get('router'));
    }
    public function resolveIncomingRequest(Request $request, RouteCollection $router)
    {

        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($router, $context);

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        // try {

        $request->attributes->add($matcher->match($request->getPathInfo()));
        $controller = $controllerResolver->getController($request);
        //$arguments = $argumentResolver->getArguments($request, $controller);
        $arguments = $this->resovleMethodArguments($request, $controller);
        // $response = call_user_func_array($controller, $arguments);
        // } catch (ResourceNotFoundException $exception) {
        //     $response = new Response('Not Found', 404);
        // } catch (Exception $exception) {
        //     $response = new Response('An error occurred', 500);
        // }
        // return $response;
    }
    public function resovleMethodArguments($request, $controller)
    {
        $resolvedController =  $this->getReflector($controller[0]);
        if (!$resolvedController->hasMethod($controller[1])) {
            return;
        }
        $method = $resolvedController->getMethod($controller[1]);
        $resolvedParameters = [];
        foreach ($method->getParameters() as $dependacy) {
            if (!$dependacy->getClass() && $dependacy->getName()) {
                $resolvedParameters[] = $request->get($dependacy->getName());
                $request->attributes->remove($dependacy->getName());
                continue;
            }
            if ($dependacy->getClass()) {

                $resolvedParameters[] = $this->get($dependacy->getClass()->getName());
            }
        }
    }
    public function registerServiceProvider($application)
    {
        $providers = require_once __DIR__ . '/../app/Provider/provider.php';
        foreach ($providers as $provider) {
            (new $provider($this))->map();
        }
    }
    public function dependancies()
    {
        return $this->dependancies;
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
        dump($name);
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
