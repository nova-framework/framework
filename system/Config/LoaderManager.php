<?php
/**
 * LoaderManager - Implements a Configuration Manager.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

namespace Config;

use Database\Connection;
use Config\DatabaseLoader;
use Config\FileLoader;


class LoaderManager implements LoaderInterface
{
    /**
     * The File Loader implementation.
     *
     * @var \Config\LoaderInterface
     */
    protected $fileLoader;

    /**
     * The Database Loader implementation.
     *
     * @var \Config\LoaderInterface
     */
    protected $dbLoader;

    /**
     * Create a new loader instance.
     *
     * @return void
     */
    function __construct($path)
    {
        $this->fileLoader = new FileLoader($path);
    }

    /**
     * Load the configuration group for the key.
     *
     * @param    string     $group
     * @return     array
     */
    public function load($group)
    {
        $items = $this->fileLoader->load($group);

        if (isset($this->dbLoader)) {
            $items = array_merge($items, $this->dbLoader->load($group));
        }

        return $items;
    }

    /**
     * Set a given configuration value using the loader implementation.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value)
    {
        // We update only the configuration value from Database
        if ($this->dbLoader) $this->dbLoader->set($key, $value);
    }

    /**
     * Set the Database Connection instance.
     *
     * @var \Database\Connection
     */
    public function setConnection(Connection $db)
    {
        $this->dbLoader = new DatabaseLoader($db);
    }

    /**
     * Set the database table for the database loader.
     *
     * @param string
     * @return void
     */
    public function setTable($table)
    {
        $this->dbLoader->setTable($table);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Database\Connection|null
     */
    public function getDBLoader()
    {
        return $this->dbLoader;
    }
}
