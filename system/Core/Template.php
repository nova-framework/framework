<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\View;


class Template extends View
{
    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($path, array $data = array())
    {
        parent::__construct($path, $data);
    }

    /**
     * Create a Template instance
     *
     * @param string $path
     * @param array $data
     * @param string $custom
     * @return Template
     */
    public static function make($path, array $data = array(), $custom = TEMPLATE)
    {
        // Prepare the file path.
        $filePath = str_replace('/', DS, "Templates/$custom/$path.php");

        return new Template(APPDIR .$filePath, $data);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * This method handles calls to dynamic with helpers.
     */
    public static function __callStatic($method, $params)
    {
        // No Compatibility Layer exists there; nothing to do.
    }

    /**
     * Compat Layer - Render a Module View file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     *
     * @throws \Exception
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        throw new \Exception('renderModule is not available on ' .static::class);
    }
}
