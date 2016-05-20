<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Base\View as BaseView;
use Support\Facades\Language as Translator;


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
        if (is_string($data)) {
            if (! empty($data) && ($template === null)) {
                // The Module name given as second parameter; adjust the information.
                $template = $data;
            }

            $data = array();
        }

        // Get the base path for the current Template files.
        $basePath = APPDIR .'Templates' .DS .$template .DS;

        // Get the name of the current Template files.
        $ltrFile = $view .'.php';
        $rtlFile = $view .'-rtl.php';

        // Use the LTR Template file by default.
        $path = $basePath .$ltrFile;

        // Depending on the Language direction, adjust to RTL Template file, if case.
        if ((Translator::direction() == 'rtl') && file_exists($basePath .$rtlFile)) {
            $path = $basePath .$rtlFile;
        }

        return new Template($view, $path, $data);
    }
}
