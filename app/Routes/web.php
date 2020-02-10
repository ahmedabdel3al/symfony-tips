<?php

namespace App\Routes;

use App\Routing\Router;

Router::get('/blog/{blog}' ,function($blog){
    dd($blog , 2222);
})->name('blog.edit');

Router::get('/blog','HomeController');