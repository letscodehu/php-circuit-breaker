<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.18.
 * Time: 4:56
 */

namespace Ejsmont\CircuitBreaker\Proxy;

use Ejsmont\CircuitBreaker\CircuitBreakerInterface;
use guymers\proxy\ProxyFactory;

class CircuitBreakerProxyFactory {

    /**
     * Creates a dynamic proxy wrapped with circuit breaker.
     *
     * @param $className string class to wrap in proxy
     * @param $circuitBreaker CircuitBreakerInterface
     * @param $constructorArgs array for the proxy constructor
     * @return dynamic proxy
     *
     */
    public static function create($className, CircuitBreakerInterface $circuitBreaker, $constructorArgs = array()) {

        $class = new \ReflectionClass($className);

        $methodOverrides = [
            new ProxyMethodHook($class, $circuitBreaker)
        ];

        return ProxyFactory::create($class, $methodOverrides, $constructorArgs);

    }


}