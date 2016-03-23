<?php
/**
 * View - View class to load template and views files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date updated Mar 17, 2016
 */

namespace Nova\Core;

use Nova\Core\Controller;
use Nova\Helpers\Inflector;
use Nova\Net\Response;


/**
 * View class to load template and views files.
 */
class View
{
    /**
     * The path to the View file on disk.
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
    protected static $shared = array();


    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     * @throws \UnexpectedValueException
     */
    public function __construct($path, array $data = array())
    {
        if (! is_readable($path)) {
            throw new \UnexpectedValueException(__d('system', 'File not found: {0}', $path));
        }

        $this->path = $path;
        $this->data = $data;
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
        if (strpos($method, 'with') === 0) {
            $name = Inflector::tableize(substr($method, 4));

            return $this->with($name, array_shift($params));
        }

        throw new \BadMethodCallException(__d('system', 'Method [{0}] is not defined on the View class', $method));
    }

    /**
     * Make view
     * @param $view
     * @return View
     */
    public static function make($view = null, array $data = array())
    {
        // Get the Controller instance.
        $controller =& get_instance();

        $viewsPath = $controller->viewsPath();

        if(is_null($view)) {
            $view = $controller->method();
        }

        // Prepare the file path.
        $path = $viewsPath .$view .'.php';

        return new View($path, $data);
    }

    /**
     * Make view layout
     * @param null $layout
     * @return View
     */
    public static function layout($layout = null, array $data = array())
    {
        // Get the Controller instance.
        $controller =& get_instance();

        $template = $controller->template();

        if(is_null($layout)) {
            $layout = $controller->layout();
        }

        // Prepare the file path.
        $path = APPPATH .'Templates' .DS .$template .DS .'Layouts' .DS .$layout .'.php';

        //
        Response::addHeader('Content-Type: text/html; charset=UTF-8');

        return new View($path, $data);
    }

    /**
     * @param $fragment
     * @param bool $fromTemplate
     * @return View
     */
    public static function fragment($fragment, array $data = array())
    {
        // Get the Controller instance.
        $controller =& get_instance();

        $template = $controller->template();

        // Prepare the file path.
        $path = APPPATH .'Templates' .DS .$template .DS .'Fragments' .DS .$fragment .'.php';

        return new View($path, $data);
    }

    public function render()
    {
        // Prepare the rendering variables from the internal data.
        foreach ($this->data() as $variable => $value) {
            ${$variable} = $value;
        }

        require $this->path;
    }

    public function fetch()
    {
        ob_start();

        $this->render();

        return ob_get_clean();
    }

    public function display()
    {
        Response::sendHeaders();

        $this->render();
    }

    public function data()
    {
        $data = array_merge($this->data, static::$shared);

        // All nested Views are evaluated before the main View.
        foreach ($data as $key => $value) {
            if ($value instanceof View) {
                $data[$key] = $value->fetch();
            }
        }

        return $data;
    }

    /**
     * Add a view instance to the view data.
     *
     * <code>
     *     // Add a view instance to a view's data
     *     $view = View::make('foo')->nest('footer', 'partials/footer');
     *
     *     // Equivalent functionality using the "with" method
     *     $view = View::make('foo')->with('footer', View::make('partials/footer'));
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

}
