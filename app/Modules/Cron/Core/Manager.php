<?php

namespace App\Modules\Cron\Core;

use App\Modules\Cron\Core\Adapter;


class Manager
{
    /**
     * The CRON Manager instance.
     */
    protected static $instance;

    /**
     * List of registered Adapters.
     */
    protected $adapters = array();


    /**
     * Create a new CRON Manager instance.
     *
     * @return void
     */
    protected function __construct()
    {
        // The constructor exists only to avoid the direct instantiation.
    }

    /**
     * Gets the CRON Manager instance.
     *
     * @return \App\Modules\Cron\Manager\Cron
     */
    protected static function getInstance()
    {
        if (! isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Gets an instance of the given Adapter name.
     *
     * @param  string  $name The Adapter name
     * @param  array   $config The Adapter's configuration array
     *
     * @return \App\Modules\Cron\Adapters\AbstractAdapter|null
     */
    protected function getAdapter($name, array $config = array())
    {
        if (! isset($this->adapters[$name])) {
            return null;
        }

        //
        $isCreated = false;

        $adapter = $this->adapters[$name];

        if (is_string($adapter) && class_exists($adapter)) {
            $adapter = new $adapter($config);

            $isCreated = true;
        }

        if ($adapter instanceof Adapter) {
            if (! $isCreated) {
                $adapter->config($config);
            }

            return $adapter;
        }

        return null;
    }

    /**
     * Gets the full list of all registered Adapters.
     */
    protected function getAdapters()
    {
        return array_keys($this->adapters);
    }

    /**
     * Registers a new Cron Adapter.
     *
     * @param string  $name The Adapter name
     * @param string  $callback The Adapter class or instance
     */
    protected function register($name, $callback)
    {
        if (array_key_exists($name, $this->adapters)) {
            $error = __d('cron', 'The Adapter <b>{0}<b> is already defined.', $name);

            throw new \LogicException($error);
        }

        $this->adapters[$name] = $callback;
    }

    /**
     * Remove a Cron Adapter.
     *
     * @param string  $name The Adapter name
     */
    protected function forget($name)
    {
        unset($this->adapters[$name]);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return void|mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getInstance();

        return call_user_func_array(array($instance, $method), $params);
    }
}
