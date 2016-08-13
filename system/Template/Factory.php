<?php

namespace Template;

use Core\Config;
use Core\Language;
use Support\Contracts\ArrayableInterface as Arrayable;
use View\Factory as ViewFactory;
use View\View;


class Factory
{
    /**
     * The View Factory instance.
     *
     * @var \View\Factory
     */
    protected $factory;

    /**
     * Create new Template Factory instance.
     *
     * @param $factory The View Factory instance.
     * @return void
     */
    function __construct(ViewFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create a View instance
     *
     * @param string $view
     * @param array|string $data
     * @param string $custom
     * @return \Nova\View\View
     */
    public function make($view, $data = array(), $template = null)
    {
        if (is_string($data)) {
            if (! empty($data) && ($template === null)) {
                // The Module name given as second parameter; adjust the information.
                $template = $data;
            }

            $data = array();
        }

        // Get the View file path.
        $path = $this->viewFile($view, $template);

        $data = $this->parseData($data);

        return new View($this->factory, $view, $path, $data, true);
    }

    /**
     * Parse the given data into a raw array.
     *
     * @param  mixed  $data
     * @return array
     */
    protected function parseData($data)
    {
        return ($data instanceof Arrayable) ? $data->toArray() : $data;
    }

    /**
     * Check if the view file exists.
     *
     * @param    string     $view
     * @return    bool
     */
    public function exists($view, $template = null)
    {
        // Get the View file path.
        $path = $this->viewFile($view, $template);

        return file_exists($path);
    }

    /**
     * Get the view file.
     *
     * @param    string     $view
     * @return    string
     */
    protected function viewFile($view, $template = null)
    {
        $language = Language::getInstance();

        // Calculate the current Template name.
        $template = $template ?: Config::get('app.template');

        if ($language->direction() == 'rtl') {
            // The current Language is RTL. Check the path of the RTL Template file.
            $filePath = str_replace('/', DS, APPDIR ."Templates/$template/$view-rtl.php");

            if (is_readable($filePath)) {
                // A valid RTL Template file found; return it.
                return $filePath;
            }
        }

        // Return the path of the current LTR Template file.
        return str_replace('/', DS, APPDIR ."Templates/$template/$view.php");
    }
}
