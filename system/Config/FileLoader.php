<?php
/**
 * FileLoader - Implements a Configuration Loader for Files storage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */
namespace Config;

use Config\Config;


class FileLoader implements LoaderInterface
{
    /**
     * Create a new FileLoader instance.
     *
     * @return void
     */
    function __construct()
    {
    }

    /**
     * Load the Configuration Group for the key.
     *
     * @param    string     $group
     * @return     array
     */
    public function load($group)
    {
        return Config::get($group, array());
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
        Config::set($key, $value);
    }
}
