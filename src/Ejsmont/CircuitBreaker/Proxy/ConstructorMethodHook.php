<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.18.
 * Time: 6:14
 */

namespace Ejsmont\CircuitBreaker\Proxy;


use guymers\proxy\MethodHook;
use ReflectionMethod;

class ConstructorMethodHook implements MethodHook {

    /**
     * Does this hook support this method
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    public function supports(ReflectionMethod $method)
    {
        return $method->getName() == "__construct";
    }

    /**
     * Called instead of the original method
     *
     * @param mixed $proxy the proxy object
     * @param ReflectionMethod $method original method
     * @param array $args original methods arguments
     */
    public function invoke($proxy, ReflectionMethod $method, array $args)
    {
        return $method->invokeArgs($proxy, $args);
    }
}