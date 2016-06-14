<?php

namespace Cache;

use Core\Config;
use Cache\ArrayStore;
use Cache\FastCacheStore;
use Cache\Repository;
use Support\Manager;


class CacheManager extends Manager
{
    /**
     * Create an instance of the APC cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createApcDriver()
    {
        return $this->repository('apc');
    }

    /**
     * Create an instance of the array cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createArrayDriver()
    {
        return $this->repository('array');
    }

    /**
     * Create an instance of the file cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createFilesDriver()
    {
        return $this->repository('files');
    }

    /**
     * Create an instance of the Memcached cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createMemcachedDriver()
    {
        return $this->repository('memcached');
    }

    /**
     * Create an instance of the WinCache cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createWincacheDriver()
    {
        return $this->repository('wincache');
    }

    /**
     * Create an instance of the XCache cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createXcacheDriver()
    {
        return $this->repository('xcache');
    }

    /**
     * Create an instance of the Redis cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createRedisDriver()
    {

        return $this->repository('redis');
    }

    /**
     * Create an instance of the database cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createSqliteDriver()
    {
        return $this->repository('sqlite');
    }

    /**
     * Create a new Cache Repository with the given implementation.
     *
     * @param  string  $storage
     * @return \Cache\Repository
     */
    protected function repository($storage)
    {
        if($storage == 'array') {
            $store = new ArrayStore();
        } else {
            $store = new FastCacheStore($storage);
        }

        return new Repository($store);
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return Config::get('cache.storage');
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        Config::set('cache.storage', $name);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->driver(), $method), $parameters);
    }

}
