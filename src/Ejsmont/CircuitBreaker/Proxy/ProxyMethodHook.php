<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.18.
 * Time: 5:21
 */

namespace Ejsmont\CircuitBreaker\Proxy;


use Ejsmont\CircuitBreaker\CircuitBreakerInterface;
use guymers\proxy\MethodHook;
use ReflectionMethod;

class ProxyMethodHook implements MethodHook {

    /**
     * @var CircuitBreakerInterface
     */
    private $circuitBreaker;
    /**
     * @var \ReflectionClass
     */
    private $classToWrap;

    private $serviceName, $timeout;


    function __construct(\ReflectionClass $classToWrap, CircuitBreakerInterface $circuitBreaker, $timeout = 3000)
    {
        $this->classToWrap = $classToWrap;
        $this->timeout = $timeout;
        $this->serviceName = $classToWrap->getName();
        $this->circuitBreaker = $circuitBreaker;
    }


    /**
     * Does this hook support this method
     *
     * @param ReflectionMethod $method
     * @return boolean
     */
    public function supports(ReflectionMethod $method)
    {
        // ignores the constructor
        return $method->getName() != "__construct";
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
        $returnValue = null;
        $oldTimeout = ini_get("default_socket_timeout");

        if ($this->circuitBreaker->isAvailable($this->serviceName)) {
            try {
                ini_set("default_socket_timeout", $this->timeout);
                $returnValue = $method->invokeArgs($proxy, $args);
                $this->circuitBreaker->reportSuccess($this->serviceName);
            } catch(\Exception $e) {
                $this->circuitBreaker->reportFailure($this->serviceName);
            }

        }
        ini_set("default_socket_timeout", $oldTimeout);
        // after original method

        return $returnValue;
    }
}