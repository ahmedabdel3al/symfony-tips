<?php

namespace App\Provider;

use App\Application;
use App\Facade\Router;

class RouteServiceProvider
{

    protected $application, $namespace = "App\Controller\\";
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {

        Router::container($this->application)->namespace($this->namespace)->load(__DIR__ . '\..\Routes\web.php')->register();
    }
}
