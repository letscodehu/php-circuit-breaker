<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.18.
 * Time: 12:47
 */

namespace Ejsmont\CircuitBreaker\Proxy;


class ProxyMethodHookTest extends \PHPUnit_Framework_TestCase {


    /**
     * @test
     */
    public function supportsIgnoresMagicFunctions()
    {
        $reflectionClass = new \ReflectionClass('Ejsmont\CircuitBreaker\Factory');
        $cb = $this->getMockBuilder('Ejsmont\CircuitBreaker\CircuitBreakerInterface')->getMock();
        // GIVEN
        $magic = array("__construct", "__destruct","__isset", "__invoke","__clone","__debugInfo", "__unset", "__sleep","__toString", "__wakeup" , "__call", "__get", "__set", "__callStatic");
        $underTest = new ProxyMethodHook($reflectionClass, $cb);
        // WHEN
        foreach ($magic as $methodName) {
            $method = new \ReflectionMethod('tests\Unit\Ejsmont\CircuitBreaker\Proxy\Test', $methodName);
            $actual = $underTest->supports($method);
            $this->assertEquals(false, $actual);
        }
    }
}
