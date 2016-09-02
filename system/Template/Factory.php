<?php

namespace Template;

use Config\Config;
use Foundation\Application;
use Language\LanguageManager;
use Support\Contracts\ArrayableInterface as Arrayable;
use Template\Template;
use View\Factory as ViewFactory;
use View\ViewFinderInterface;
use View\View;


class Factory
{
    /**
     * The Application instance.
     *
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * The view finder implementation.
     *
     * @var \View\ViewFinderInterface
     */
    protected $finder;

    /**
     * Create new Template Factory instance.
     *
     * @param $factory The View Factory instance.
     * @return void
     */
    function __construct(Application $app, ViewFinderInterface $finder)
    {
        $this->app = $app;

        $this->finder = $finder;
    }

    /**
     * Create a View instance
     *
     * @param string $view
     * @param array|string $data
     * @param string $custom
     * @return \View\View
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
        $path = $this->find($view, $template);

        // Get the parsed data.
        $data = $this->parseData($data);

        // Get the View Factory instance.
        $factory = $this->getViewFactory();

        return new Template($factory, $view, $path, $data);
    }

    /**
     * Check if the view file exists.
     *
     * @param    string     $view
     * @return    bool
     */
    public function exists($view, $template = null)
    {
        try {
            $this->find($view, $template);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
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
     * Find the View file.
     *
     * @param    string     $view
     * @param    string     $template
     * @return    string
     */
    protected function find($view, $template = null)
    {
        // Calculate the current Template name.
        $template = $template ?: Config::get('app.template');

        // Calculate the search path.
        $path = sprintf('Templates/%s/%s', $template, $view);

        // Make the path absolute and adjust the directory separator.
        $path = str_replace('/', DS, APPDIR .$path);

        // Find the View file depending on the Language direction.
        $language = $this->getLanguage();

        if ($language->direction() == 'rtl') {
            // Search for the View file used on the RTL languages.
            $filePath = $this->finder->find($path .'-rtl');
        } else {
            $filePath = null;
        }

        if (is_null($filePath)) {
            $filePath = $this->finder->find($path);
        }

        if (! is_null($filePath)) return $filePath;

        throw new \InvalidArgumentException("Unable to load the view '" .$view ."' on template '" .$template ."'.", 1);
    }

    /**
     * Return the current View Factory instance.
     *
     * @return \View\Factory
     */
    protected function getViewFactory()
    {
        return $this->app['view'];
    }

    /**
     * Return the current Language instance.
     *
     * @return \Language\Language
     */
    protected function getLanguage()
    {
        $languages = $this->app['language'];

        return $languages->instance();
    }
}
