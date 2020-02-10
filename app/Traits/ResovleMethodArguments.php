<?php

namespace App\Traits;

use ReflectionFunction;
use Closure;

/**
 * 
 */
trait ResovleMethodArguments
{
    /**
     * Resolve Method Arguments
     *
     * @param [type] $request
     * @param [type] $controller
     * @return void
     */
    public function resovleMethodArguments($request, $controller): array
    {
       
        $resolvedParameters = [];
       
        if (!is_array($controller)) {
            return $resolvedParameters;
        }
        if ($controller[0] instanceof Closure) {
            $reflection = new ReflectionFunction($controller[0]);
            return  $this->getParamters($reflection, $request) ;
        }
        $resolvedController =  $this->getReflector($controller[0]);
        if (!$resolvedController->hasMethod($controller[1])) {
            return $resolvedController;
        }
        $method = $resolvedController->getMethod($controller[1]);
        return $this->getParamters($method, $request);
    }
    public function getParamters($method, $request)
    {
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
        return $resolvedParameters;
    }
}
