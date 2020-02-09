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

/**
 * Create request from global
 */ 
$request = Request::createFromGlobals();
$app = new  Application();
$response = $app->handle($request);
//$response->send();













