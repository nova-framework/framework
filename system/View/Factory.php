<?php

namespace View;

use Foundation\Application;
use Support\Contracts\ArrayableInterface as Arrayable;
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
     * @var array Array of shared data
     */
    protected $shared = array();


    /**
     * Create new View Factory instance.
     *
     * @return void
     */
    function __construct(Application $app)
    {
        $this->app = $app;
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
        $path = $this->viewFile($view, $module);

        $data = $this->parseData($data);

        return new View($this, $view, $path, $data);
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
        // Get the View file path.
        $path = $this->viewFile($view, $module);

        return file_exists($path);
    }

    /**
     * Get the view file.
     *
     * @param    string     $view
     * @return    string
     */
    protected function viewFile($view, $module = null)
    {
        // Prepare the (relative) file path according with Module parameter presence.
        if ($module !== null) {
            $path = str_replace('/', DS, APPDIR ."Modules/$module/Views/$view.php");
        } else {
            $path = str_replace('/', DS, APPDIR ."Views/$view.php");
        }

        return $path;
    }
}
