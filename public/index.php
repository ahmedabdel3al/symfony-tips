<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use App\Application;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;



$request = Request::createFromGlobals();
$app= new Application;
// looks inside *this* directory
$fileLocator = new FileLocator([__DIR__]);
$loader = new YamlFileLoader($fileLocator);
$routes = $loader->load('routes.yaml');
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);
$matched = $matcher->match($request->getPathInfo());
$parts = explode('::',$matched['_controller']);

unset($matched['_controller'],$matched['_route']);

(new $parts[0])->{$parts[1]}(array_values($matched));


















// $route = new Route('/blog/{slug}', ['_controller' => BlogController::class , '_method'=>'index']);
// $routes = new RouteCollection();
// $routes->add('blog_show', $route);
// $context = new RequestContext();
// $context->fromRequest($request);
// $matcher = new UrlMatcher($routes, $context);

// $match = $matcher->match($request->getPathInfo());
// $controller = $match['_controller'];
// $method = $match['_method'];
// unset($match['_controller']);
// unset($match['_method']);
// unset($match['_route']);

// (new $controller)->{$method}(...array_values($match));

// $app->route('/', function () {
//     return new Response('This is the home page');
// });

// $app->route('/about', function () {
//     return new Response('This is the about page');
// });

// $app->route('/about/{id}', function ($id) {
//     return new Response("This is the about page with id:  $id");
// });
//$response = $app->handle($request);
//$response->send();