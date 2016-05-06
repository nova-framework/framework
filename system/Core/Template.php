<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\BaseView;

/**
 * View class to load templates files.
 */
class Template extends BaseView
{
    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     *
     * @throws \UnexpectedValueException
     */
    protected function __construct($path, array $data = array())
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
}
