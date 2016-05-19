<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Base\View as BaseView;


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
    protected function __construct($view, $path, array $data = array())
    {
        parent::__construct($view, $path, $data);
    }

    /**
     * Create a Template instance
     *
     * @param string $view
     * @param array|string $data
     * @param string $custom
     * @return Template
     */
    public static function make($view, $data = array(), $template = TEMPLATE)
    {
        if(is_string($data)) {
            if(! empty($data) && ($template === null)) {
                // The Module name given as second parameter; adjust the information.
                $template = $data;
            }

            $data = array();
        }

        // Prepare the file path.
        $path = str_replace('/', DS, APPDIR ."Templates/$template/$view.php");

        return new Template($view, $path, $data);
    }
}
