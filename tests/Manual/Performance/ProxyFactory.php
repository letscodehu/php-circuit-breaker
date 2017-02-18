<?php

namespace Tests\Manual\Performance;

use Ejsmont\CircuitBreaker\Proxy\CircuitBreakerProxyFactory;
use Ejsmont\CircuitBreaker\Factory;
use Predis\Client;

require dirname(__FILE__) . '/../../../vendor/autoload.php';

class ParentClass {

    public function __construct($something = null, $test = NULL) {
        $this->something = $something;
    }

}

class SomeSlowService extends ParentClass {

    private $something;

    public function __construct($something = null) {
        $this->something = $something;
    }

    public function getStuff() {
        return $this->something;
    }

}


$client = new Client("tcp://127.0.0.1:6379?read_write_timeout=0");

$factory = new Factory();
$cb = $factory->getRedisInstance($client, 30, 3600);

$start = microtime(true);
$proxy = CircuitBreakerProxyFactory::create('Tests\Manual\Performance\SomeSlowService', $cb, ["test"]);
$stop = microtime(true);

echo sprintf("Total time for creating the proxy: %.4f\n", $stop - $start);

$nonProxy = new SomeSlowService("test");

$callCount = 10000;

$start = microtime(true);
for ($i = 0; $i < $callCount; $i++) {
    $proxy->getStuff();
}
$stop = microtime(true);

echo sprintf("Total time for %d calls with proxy: %.4f\n", $callCount, $stop - $start);

$start = microtime(true);
for ($i = 0; $i < $callCount; $i++) {
    $nonProxy->getStuff();
}
$stop = microtime(true);

echo sprintf("Total time for %d calls without proxy: %.4f\n", $callCount, $stop - $start);
