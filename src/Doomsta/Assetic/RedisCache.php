<?php


namespace Doomsta\Silex;


use Assetic\Cache\CacheInterface;

class RedisCache implements CacheInterface
{
    public function __construct()
    {
    }


    /**
     * Checks if the cache has a value for a key.
     *
     * @param string $key A unique key
     *
     * @return Boolean Whether the cache has a value for this key
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * Returns the value for a key.
     *
     * @param string $key A unique key
     *
     * @return string|null The value in the cache
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * Sets a value in the cache.
     *
     * @param string $key A unique key
     * @param string $value The value to cache
     */
    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * Removes a value from the cache.
     *
     * @param string $key A unique key
     */
    public function remove($key)
    {
        // TODO: Implement remove() method.
    }
}
 