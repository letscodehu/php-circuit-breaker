<?php

namespace Tests\Manual\Performance;

use Ejsmont\CircuitBreaker\Factory;

require dirname(__FILE__) . '/../../SplClassLoader.php';
require dirname(__FILE__) . '/../../../vendor/predis/predis/src/Autoloader.php';

\Predis\Autoloader::register();

$callCount = 10000;

$autoLoader = new \SplClassLoader('Ejsmont', dirname(__FILE__) . '/../../../src');
$autoLoader->register();

$client = new \Predis\Client();
$client->del("EjsmontCircuitBreakerCircuitBreakerStatsAggregatedStats");

$factory = new Factory();
$cb = $factory->getRedisInstance($client, 30, 3600);

$start = microtime(true);
for ($i = 0; $i < $callCount; $i++) {
    $serviceName = "someServiceName" . ($i % 5);
    $cb->isAvailable($serviceName);
    if (mt_rand(1, 1000) > 700) {
        $cb->reportSuccess($serviceName);
    } else {
        $cb->reportFailure($serviceName);
    }
}
$stop = microtime(true);

print_r(array(
    sprintf("Total time for %d calls: %.4f", $callCount, $stop - $start),
    unserialize($client->get("EjsmontCircuitBreakerCircuitBreakerStatsAggregatedStats")),
));
