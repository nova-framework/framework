<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated Dec 18, 2015
 */

namespace Nova\Core;

use Nova\Core\Controller;
use Nova\Helpers\Inflector;
use Nova\Net\Response;

// TODO: Add more PHPDoc Blocks

/**
 * View class to load template and views files.
 */
class View
{
    /**
     * The path to the view on disk.
     *
     * @var string
     */
    protected $path = null;

    /**
     * The view data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * All of the shared view data.
     *
     * @var array
     */
    public static $shared = array();

    protected $json = false;

    /**
     * Constructor
     * @param array $param
     * @param mixed $json
     * @throws \UnexpectedValueException
     */
    public function __construct($param, $json = false)
    {
        if (! $json) {
            $this->path = $param;

            return;
        }

        if (! is_array($param)) {
            throw new \UnexpectedValueException(__d('system', 'Parameter should be an Array'));
        }

        $this->json = true;
        $this->data = $param;
    }

    /**
     * Magic Method for handling dynamic data access.
     */
    public function __get($key)
    {
        return $this->data[$key];
    }

    /**
     * Magic Method for handling the dynamic setting of data.
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Magic Method for checking dynamically-set data.
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get the evaluated string content of the View.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fetch();
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * This method handles calls to dynamic with helpers.
     */
    public function __call($method, $params)
    {
        if (strpos($method, 'with') !== 0) {
            throw new \BadMethodCallException(__d('system', 'Invalid method called: View::{0}', $method));
        }

        $name = Inflector::tableize(substr($method, 4));

        return $this->with($name, array_shift($params));
    }

    /**
     * Make view
     * @param $view
     * @return View
     * @throws \UnexpectedValueException
     */
    public static function make($view, $data = array())
    {
        $filePath = self::viewPath($view);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException(__d('system', 'File not found: {0}', $filePath));
        }

        return new View($filePath);
    }

    /**
     * Make view layout
     * @param null $layout
     * @return View
     * @throws \UnexpectedValueException
     */
    public static function layout($layout = null, $data = array())
    {
        $filePath = self::layoutPath($layout);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException(__d('system', 'File not found: {0}', $filePath));
        }

        Response::addHeader('Content-Type: text/html; charset=UTF-8');

        return new View($filePath);
    }

    /**
     * @param $fragment
     * @param bool $fromTemplate
     * @return View
     * @throws \UnexpectedValueException
     */
    public static function fragment($fragment, $fromTemplate = true)
    {
        $filePath = self::fragmentPath($fragment, $fromTemplate);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException(__d('system', 'File not found: {0}', $filePath));
        }

        return new View($filePath);
    }

    /**
     * @param $data
     * @return View
     * @throws \UnexpectedValueException
     */
    public static function json($data)
    {
        if (! is_array($data)) {
            throw new \UnexpectedValueException(__d('system', 'Unexpected parameter'));
        }

        Response::addHeader('Content-Type: application/json');

        return new View($data, true);
    }

    public function isJson()
    {
        return $this->json;
    }

    public function fetch()
    {
        $data = $this->data();

        if ($this->json) {
            return json_encode($data);
        }

        // Prepare the rendering variables.
        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        // Execute the rendering, then capture and return the output.
        ob_start();

        require $this->path;

        return ob_get_clean();
    }

    public function render()
    {
        Response::sendHeaders();

        echo $this->fetch();
    }

    public function data()
    {
        $data = array_merge($this->data, static::$shared);

        // All nested views and responses are evaluated before the main view.
        // This allows the assets used by nested views to be added to the
        // asset container before the main view is evaluated.
        foreach ($data as $key => $value) {
            if ($value instanceof View) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Add a view instance to the view data.
     *
     * <code>
     *              // Add a view instance to a view's data
     *              $view = View::make('foo')->nest('footer', 'partials.footer');
     *
     *              // Equivalent functionality using the "with" method
     *              $view = View::make('foo')->with('footer', View::make('partials.footer'));
     * </code>
     *
     * @param  string  $key
     * @param  string  $view
     * @param  array   $data
     * @return View
     */
    public function nest($key, $view, $data = array())
    {
        return $this->with($key, static::make($view, $data));
    }

    /**
     * Add a key / value pair to the view data.
     *
     * Bound data will be available to the view as variables.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return View
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Add a key / value pair to the shared view data.
     *
     * Shared view data is accessible to every view created by the application.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return View
     */
    public function shares($key, $value)
    {
        static::share($key, $value);

        return $this;
    }

    /**
     * Add a key / value pair to the shared view data.
     *
     * Shared view data is accessible to every view created by the application.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public static function share($key, $value)
    {
        static::$shared[$key] = $value;
    }

    private static function viewPath($path)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        //
        $basePath = $instance->viewsPath();

        return $basePath .$path .'.php';
    }

    private static function templatePath($template = null)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if(is_null($template)) {
            $template = $instance->template();
        }

        return APPPATH .'Templates' .DS .$template .DS;
    }

    private static function layoutPath($layout = null, $template = null)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if(is_null($layout)) {
            $layout = $instance->layout();
        }

        $basePath = self::templatePath($template);

        // Adjust the filePath for Layouts
        return $basePath .'Layouts' .DS .$layout .'.php';
    }

    private static function fragmentPath($fragment, $fromTemplate = true)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        //
        $module = $instance->module();

        if ($fromTemplate) {
            // On Template path.
            $basePath = self::templatePath();
        } else if($module !== null) {
            // On Modules path.
            $basePath = APPPATH .'Modules' .DS.$module .DS;
        } else {
            // On Default path.
            $basePath = APPPATH .'Views'.DS;
        }

        // Adjust the filePath for Fragments
        return $basePath .'Fragments' .DS .$fragment .'.php';
    }

}
