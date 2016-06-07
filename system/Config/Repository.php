<?php
/**
 * Repository - Implements a Configuration Repository.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

namespace Config;


class Repository implements \ArrayAccess
{
    /**
     * The loader implementation.
     *
     * @var \Config\LoaderInterface
     */
    protected $loader;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = array();

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) !== $default;
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = false)
    {
        @list($group, $item) = $this->parseKey($key);

        $this->load($group);

        if (empty($item)) {
            return $this->items[$group];
        }

        return array_get($this->items[$group], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value)
    {
        @list($group, $item) = $this->parseKey($key);

        $this->load($group);

        if (empty($item)) {
            $this->items[$group] = $value;
        } else {
            array_set($this->items[$group], $item, $value);
        }

        $this->loader->set($key, $value);
    }

    /**
     * Load the configuration group for the key.
     *
     * @param    string     $group
     * @return     void
     */
    public function load($group)
    {
        if (isset($this->items[$group])) return;

        $this->items[$group] = $this->loader->load($group);
    }

    /**
     * Parse a key into group, and item.
     *
     * @param  string  $key
     * @return array
     */
    public function parseKey($key)
    {
        $segments = explode('.', $key);

        $group = $segments[0];

        unset($segments[0]);

        $segments = implode('.', $segments);

        return array($group, $segments);
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the loader manager instance.
     *
     * @return \Config\LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
