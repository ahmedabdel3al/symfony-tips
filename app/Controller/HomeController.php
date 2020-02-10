<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    protected $request ;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function __invoke()
    {
        dd('boly fci');
    }
    // public function index($blog, Request $request)
    // {
    //     dd($blog , $request);
    //     return new Response('inside response');
    // }
    // public function  __invoke()
    // {
    //    dd('boly fci');   
    // }
}
