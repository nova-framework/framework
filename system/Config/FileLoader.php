<?php
/**
 * FileLoader - Implements a Configuration Loader for Files storage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */
namespace Config;

use Core\Config as CoreConfig;


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
        return CoreConfig::get($group, array());
    }
}
