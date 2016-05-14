<?php
/**
 * FastCache - A simple Cache Management built on top of phpFastCache.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Helpers;

use Core\Config;

use \phpFastCache;


/**
 * FastCache
 */
class FastCache
{
    // The FastCache instances.
    protected static $instances = array();

    // The phpFastCahe instance.
    protected $cache = null;


    /**
     * Constructor.
     *
     * @param string $storage The Storage type.
     */
    protected function __construct($storage)
    {
        $config = Config::get('cache');

        $config['storage'] = $storage;

        $storage = strtolower($storage);

        if (empty($storage) || ($storage == 'auto')) {
            $storage = phpFastCache::getAutoClass($config);
        }

        $this->cache = phpFastCache($storage, $config);
    }

    /**
     * Get the FastCache instance of specified Storage type.
     *
     * @param string $storage
     * @return mixed
     */
    public static function getInstance($storage = 'files')
    {
        if (! isset(self::$instances[$storage])) {
            static::$instances[$storage] = new static($storage);
        }

        return static::$instances[$storage];
    }

    /**
     * Provide transparent access to any of \phpFastCache Methods.
     *
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params = null)
    {
        return call_user_func_array(array($this->cache, $method), $params);
    }
}
