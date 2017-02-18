<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.18.
 * Time: 4:56
 */

namespace Ejsmont\CircuitBreaker\Proxy;

use Ejsmont\CircuitBreaker\CircuitBreakerInterface;
use guymers\proxy\MethodHook;
use guymers\proxy\ProxyFactory;

class CircuitBreakerProxyFactory {

    /**
     * Creates a dynamic proxy wrapped with circuit breaker.
     *
     * @param $className string class to wrap in proxy
     * @param $circuitBreaker CircuitBreakerInterface
     * @param $constructorArgs array for the proxy constructor
     * @param $methodHook MethodHook to override the default
     * @return mixed proxy
     *
     */
    public static function create($className, CircuitBreakerInterface $circuitBreaker, $constructorArgs = array(), $methodHook = null) {

        $class = new \ReflectionClass($className);
        if ($methodHook == null) {
            $methodHook = new ProxyMethodHook($class, $circuitBreaker);
        }

        $methodOverrides = [
            $methodHook
        ];

        return ProxyFactory::create($class, $methodOverrides, $constructorArgs);

    }


}