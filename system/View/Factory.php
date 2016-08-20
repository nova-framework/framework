<?php

namespace View;

use Support\Contracts\ArrayableInterface as Arrayable;
use View\Engines\EngineResolver;
use View\View;
use View\ViewFinderInterface;


class Factory
{
    /**
     * The Engines Resolver instance.
     *
     * @var \View\Engines\EngineResolver
     */
    protected $engines;

    /**
     * The view finder implementation.
     *
     * @var \View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var array Array of shared data
     */
    protected $shared = array();

    /**
     * The extension to Engine bindings.
     *
     * @var array
     */
    protected $extensions = array('php' => 'php');


    /**
     * Create new View Factory instance.
     *
     * @return void
     */
    function __construct(EngineResolver $resolver, ViewFinderInterface $finder)
    {
        $this->engines = $resolver;
        $this->finder  = $finder;

        //
        $this->share('__env', $this);
    }

    /**
     * Create a View instance
     *
     * @param string $path
     * @param array|string $data
     * @param string|null $module
     * @return \Nova\View\View
     */
    public function make($view, $data = array(), $module = null)
    {
        if (is_string($data)) {
            if (! empty($data) && ($module === null)) {
                // The Module name given as second parameter; adjust the information.
                $module = $data;
            }

            $data = array();
        }

        // Get the View file path.
        $path = $this->find($view, $module);

        // Get the View Engine instance.
        $engine = $this->getEngineFromPath($path);

        // Get the parsed data.
        $data = $this->parseData($data);

        return new View($this, $engine, $view, $path, $data);
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
     * Add a piece of shared data to the Factory.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function share($key, $value = null)
    {
        if ( ! is_array($key)) return $this->shared[$key] = $value;

        foreach ($key as $innerKey => $innerValue) {
            $this->share($innerKey, $innerValue);
        }
    }

    /**
     * Get an item from the shared data.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function shared($key, $default = null)
    {
        return array_get($this->shared, $key, $default);
    }

    /**
     * Get all of the shared data for the Factory.
     *
     * @return array
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Check if the view file exists.
     *
     * @param    string     $view
     * @return    bool
     */
    public function exists($view, $module = null)
    {
        try {
            $this->find($view, $module);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the appropriate View Engine for the given path.
     *
     * @param  string  $path
     * @return \View\Engines\EngineInterface
     */
    public function getEngineFromPath($path)
    {
        $extension = $this->getExtension($path);

        $engine = $this->extensions[$extension];

        return $this->engines->resolve($engine);
    }

    /**
     * Get the extension used by the view file.
     *
     * @param  string  $path
     * @return string
     */
    protected function getExtension($path)
    {
        $extensions = array_keys($this->extensions);

        return array_first($extensions, function($key, $value) use ($path)
        {
            return str_ends_with($path, $value);
        });
    }

    /**
     * Register a valid view extension and its engine.
     *
     * @param  string   $extension
     * @param  string   $engine
     * @param  Closure  $resolver
     * @return void
     */
    public function addExtension($extension, $engine, $resolver = null)
    {
        $this->finder->addExtension($extension);

        if (isset($resolver)) {
            $this->engines->register($engine, $resolver);
        }

        unset($this->extensions[$extension]);

        $this->extensions = array_merge(array($extension => $engine), $this->extensions);
    }

    /**
     * Get the extension to engine bindings.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Get the engine resolver instance.
     *
     * @return \View\Engines\EngineResolver
     */
    public function getEngineResolver()
    {
        return $this->engines;
    }

    /**
     * Get the View Finder instance.
     *
     * @return \View\ViewFinderInterface
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * Set the View Finder instance.
     *
     * @return void
     */
    public function setFinder(ViewFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Find the view file.
     *
     * @param    string      $view
     * @param    string|null $module
     * @return    string
     */
    protected function find($view, $module = null)
    {
        if (! is_null($module)) {
            $path = "Modules/$module/Views/$view";
        } else {
            $path = "Views/$view";
        }

        // Make the path absolute and adjust the directory separator.
        $path = str_replace('/', DS, APPDIR .$path);

        //
        $filePath = $this->finder->find($path);

        if (! is_null($filePath)) return $filePath;

        throw new \InvalidArgumentException("Unable to load the view '" .$view ."' on domain '" .($module ?: 'App')."'.", 1);
    }
}
