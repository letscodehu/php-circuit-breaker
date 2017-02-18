<?php

namespace Ejsmont\CircuitBreaker\Storage\Adapter;


use Ejsmont\CircuitBreaker\Storage\StorageException;
use Predis\Client;
use Predis\Connection\ConnectionException;

class RedisAdapter extends BaseAdapter {

    /**
     * @var \Redis redis client instance
     */
    private $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct(Client $redis, $ttl = 3600, $cachePrefix = false) {
        parent::__construct($ttl, $cachePrefix);
        $this->redis = $redis;
    }

    /**
     * Helper method to make sure that extension is loaded (implementation dependent)
     *
     * @throws \Ejsmont\CircuitBreaker\Storage\StorageException if extension is not loaded
     * @return void
     */
    protected function checkExtension()
    {
        // It would fail at constructor.
    }

    /**
     * Loads item by cache key.
     *
     * @param string $key
     * @return mixed
     *
     * @throws \Ejsmont\CircuitBreaker\Storage\StorageException if storage error occurs, handler can not be used
     */
    protected function load($key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (ConnectionException $e) {
            throw new StorageException("Failed to get redis key: $key", 1, $e);
        }
    }

    /**
     * Save item in the cache.
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return void
     *
     * @throws \Ejsmont\CircuitBreaker\Storage\StorageException if storage error occurs, handler can not be used
     */
    protected function save($key, $value, $ttl)
    {
        try {
            $this->redis->set($key, serialize($value));
            $this->redis->expireAt($key, $ttl);
        } catch (ConnectionException $e) {
            throw new StorageException("Failed to save redis key: $key", 1, $e);
        }
    }
}