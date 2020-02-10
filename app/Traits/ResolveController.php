<?php 
namespace App\Traits ;

use Symfony\Component\HttpFoundation\Request;
use Closure;

trait ResolveController {

    /**
     * Resolve Controller and method 
     * @return  array 
     */
    public function resolveController(Request $request):array{

       if($request->attributes->get('_controller') instanceof Closure){
         return [$request->attributes->get('_controller') , "" ];
       }
        @[$controller , $method ] = explode('::',$request->attributes->get('_controller'));
        return [$controller , $method];
    }
}