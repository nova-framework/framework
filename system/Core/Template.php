<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Config;
use Core\BaseView;
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
    public static function make($view, $data = array(), $template = null)
    {
        list($data, $template) = static::parseParams($data, $template);

        // Adjust the current Template.
        $template = $template ?: Config::get('app.template');

        // Get the base path for the current Template files.
        $basePath = APPDIR .'Templates' .DS .$template .DS;

        // Get the name of the current Template files.
        $ltrPath = $basePath .$view .'.php';
        $rtlPath = $basePath .$view .'-rtl.php';

        // Depending on the Language direction, adjust to RTL Template file, if case.
        if ((Translator::direction() == 'rtl') && file_exists($rtlPath)) {
            $path = $rtlPath;
        } else {
            $path = $ltrPath;
        }

        return new Template($view, $path, $data);
    }
}
