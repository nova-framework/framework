<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Base\View as BaseView;
use Support\Facades\Language as CoreLanguage;


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

        // Get the current Language direction.
        $direction = CoreLanguage::direction();

        // Prepare the Template file names.
        $ltrFile = $view .'.php';
        $rtlFile = $view .'-rtl.php';

        // Get the right file for the current Layout, considering the Language directions.
        $viewFile = (($direction == 'rtl') && file_exists($basePath .$rtlFile)) ? $rtlFile : $ltrFile;

        return new Template($view, $basePath .$viewFile, $data);
    }
}
