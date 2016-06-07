<?php
/**
 * FileLoader - Implements a Configuration Loader for Files storage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */
namespace Config;


class FileLoader implements LoaderInterface
{
    /**
     *  The path to the config files.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new FileLoader instance.
     *
     * @return void
     */
    function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Load the Configuration Group for the key.
     *
     * @param    string     $group
     * @return     array
     */
    public function load($group)
    {
        $items = array();

        //
        $path = rtrim($this->path, '/');

        foreach (array('/Local', '/Testing', '') as $dir) {
            $file = $path ."{$dir}/{$group}.php";

            if (is_readable($file)) {
                return (array) include $file;
            }
        }

        return $items;
    }
}
