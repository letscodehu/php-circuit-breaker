<?php
/**
 * Created by PhpStorm.
 * User: tacsiazuma
 * Date: 2017.02.17.
 * Time: 23:53
 */

namespace tests\Unit\Ejsmont\CircuitBreaker\Storage\Adapter;


use Ejsmont\CircuitBreaker\Storage\Adapter\RedisAdapter;

class RedisAdapterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var RedisAdapter
     */
    private $underTest;

    private $mockRedis;

    public function setUp() {
        $this->mockRedis = $this->getMockBuilder('Predis\Client')->setMethods(["get", "set", "expireAt"])->getMock();
        $this->underTest = new RedisAdapter($this->mockRedis);
    }

    /**
     * @test
     */
    public function test_it_should_invoke_and_serialize_when_loadStatus() {
        // GIVEN
        $this->mockRedis
            ->expects($this->once())
            ->method("get")
            ->with("EjsmontCircuitBreakerServiceTest")
            ->willReturn(serialize("test"));
        // WHEN
        $actual = $this->underTest->loadStatus("Service","Test");
        // THEN
        $this->assertEquals("test", $actual);
    }

    /**
     * @test
     */
    public function test_it_should_set_expiration_when_Savestatus() {
        // GIVEN
        $this->mockRedis
            ->expects($this->once())
            ->method("set")
            ->with("EjsmontCircuitBreakerServiceTest", serialize("Value"));

        $this->mockRedis->expects($this->once())
            ->method("expireAt")
            ->with("EjsmontCircuitBreakerServiceTest", 3600);
        // WHEN
        $this->underTest->saveStatus("Service","Test", "Value");
        // THEN
    }


    /**
     * @test
     * @expectedException \Ejsmont\CircuitBreaker\Storage\StorageException
     */
    public function testFailSave() {
        // GIVEN
        $this->mockRedis
            ->expects($this->once())
            ->method("set")
            ->will($this->throwException(new \Exception("some error")));
        // WHEN
        $this->underTest->saveStatus('someService', 'someValue', 951);
        // THEN exception is thrown
    }

    /**
     * @test
     * @expectedException \Ejsmont\CircuitBreaker\Storage\StorageException
     */
    public function testFailLoad() {
        // GIVEN
        $this->mockRedis
            ->expects($this->once())
            ->method("get")
            ->will($this->throwException(new \Exception("some error")));
        // WHEN
        $this->underTest->loadStatus('someService', 'someValue');
        // THEN exception is thrown
    }

}