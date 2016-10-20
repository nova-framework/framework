<?php
/**
 * LoaderManager - Implements a Configuration Manager.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

namespace Nova\Config;

use Nova\Database\Connection;
use Nova\Config\DatabaseLoader;
use Nova\Config\FileLoader;


class LoaderManager implements LoaderInterface
{
    /**
     * The File Loader implementation.
     *
     * @var \Nova\Config\LoaderInterface
     */
    protected $fileLoader;

    /**
     * The Database Loader implementation.
     *
     * @var \Nova\Config\LoaderInterface
     */
    protected $dbLoader;

    /**
     * Create a new loader instance.
     *
     * @return void
     */
    function __construct()
    {
        $this->fileLoader = new FileLoader();
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
        if (isset($this->dbLoader)) {
            $this->dbLoader->set($key, $value);
        } else {
            $this->fileLoader->set($key, $value);
        }
    }

    /**
     * Set the Database Connection instance.
     *
     * @var \Nova\Database\Connection
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
     * @return \Nova\Database\Connection|null
     */
    public function getDBLoader()
    {
        return $this->dbLoader;
    }
}
