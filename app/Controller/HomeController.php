<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    // public $request;
    // public function __construct(Request $request)
    // {
    //     $this->request = $request;
    // }
    public function index($blog, Request $request)
    {
        return new Response('inside response');
    }
}
