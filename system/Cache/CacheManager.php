<?php

namespace Cache;

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
        $config = $this->app['config']['cache'];

        return $this->repository('apc', $config);
    }

    /**
     * Create an instance of the array cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createArrayDriver()
    {
        return $this->repository(new ArrayStore);
    }

    /**
     * Create an instance of the file cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createFilesDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('files', $config);
    }

    /**
     * Create an instance of the Memcached cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createMemcachedDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('memcached', $config);
    }

    /**
     * Create an instance of the WinCache cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createWincacheDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('wincache', $config);
    }

    /**
     * Create an instance of the XCache cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createXcacheDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('xcache', $config);
    }

    /**
     * Create an instance of the Redis cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createRedisDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('redis', $config);
    }

    /**
     * Create an instance of the database cache driver.
     *
     * @return \Cache\Repository
     */
    protected function createSqliteDriver()
    {
        $config = $this->app['config']['cache'];

        return $this->repository('sqlite', $config);
    }

    /**
     * Create a new Cache Repository with the given implementation.
     *
     * @param  string  $storage
     * @return \Cache\Repository
     */
    protected function repository($storage, array $config)
    {
        if(is_string($storage)) {
            $storage = new FastCacheStore($storage, $config);
        }

        return new Repository($storage);
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['cache.storage'];
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['cache.storage'] = $name;
    }

}
