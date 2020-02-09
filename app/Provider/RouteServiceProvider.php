<?php 

namespace App\Provider;

use App\Application;
use App\Routes\WebRoute;

class RouteServiceProvider {
    
    protected $application ;
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
      $router = $this->application->get('router') ;
      (new WebRoute)->register($router);
      
    }


}