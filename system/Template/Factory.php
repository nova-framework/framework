<?php

namespace Template;

use Core\Config;
use Core\Language;
use Foundation\Application;
use Support\Contracts\ArrayableInterface as Arrayable;
use View\Factory as ViewFactory;
use View\ViewFinderInterface;
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
    function __construct(ViewFactory $factory, ViewFinderInterface $finder)
    {
        $this->factory = $factory;
        $this->finder  = $finder;
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
        $path = $this->find($view, $template);

        // Get the View Engine instance.
        $engine = $this->getEngineFromPath($path);

        // Get the parsed data.
        $data = $this->parseData($data);

        return new View($this->factory, $engine, $view, $path, $data, true);
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
     * Get the appropriate View Engine for the given path.
     *
     * @param  string  $path
     * @return \View\Engines\EngineInterface
     */
    protected function getEngineFromPath($path)
    {
        return $this->factory->getEngineFromPath($path);
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
        $language = Language::getInstance();

        $suffix = ($language->direction() == 'rtl') ? '-rtl' : '';

        // Calculate the current Template name.
        $template = $template ?: Config::get('app.template');

        $path = sprintf('Templates/%s/%s%s', $template, $view, $suffix);

        // Make the path absolute and adjust the directory separator.
        $path = str_replace('/', DS, APPDIR .$path);

        //
        $filePath = $this->finder->find($path);

        if (! is_null($filePath)) return $filePath;

        throw new \InvalidArgumentException("Unable to load the view '" .$view ."' on template '" .$template ."'.", 1);
    }
}
