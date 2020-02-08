<?php 

namespace App ;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class Application implements HttpKernelInterface{

    protected $routes = [];
    
    public function __construct()
    {
        $this->routes = new RouteCollection ;
    }
    public function handle(\Symfony\Component\HttpFoundation\Request $request, int $type = self::MASTER_REQUEST, bool $catch = true)
    {
        // create a context using the current request
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);
        try{
            $attributes = $matcher->match($request->getPathInfo());
            $controller = $attributes['controller'];
            unset($attributes['controller']);
			$response = call_user_func_array($controller, $attributes); 
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not found!', Response::HTTP_NOT_FOUND);
        }
        return $response;
      
    }
    public function route($path, $controller) {
        $this->routes->add($path, new Route(
            $path,
            array('controller' => $controller)
        ));
    }
    
}