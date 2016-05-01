<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Helpers\Inflector;
use Helpers\Response;
use Core\Template;

use ArrayAccess;


/**
 * View class to load template and views files.
 */
class View implements ArrayAccess
{
    /**
     * @var array Array of shared data
     */
    protected static $shared = array();

    /**
     * @var string The path to the View file on disk.
     */
    protected $path = null;

    /**
     * @var array Array of local data.
     */
    protected $data = array();

    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($path, array $data = array())
    {
        if (! is_readable($path)) {
            throw new \UnexpectedValueException('File not found: ' .$path);
        }

        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Create a View instance
     *
     * @param string $path
     * @param array $data
     * @param string|null $module
     * @return View
     */
    public static function make($path, array $data = array(), $module = null)
    {
        // Prepare the (relative) file path according with Module parameter presence.
        if ($module !== null) {
            $filePath = str_replace('/', DS, "Modules/$module/Views/$path.php");
        } else {
            $filePath = str_replace('/', DS, "Views/$path.php");
        }

        return new View(APPDIR .$filePath, $data);
    }

    /**
     * Render the View and return the result.
     *
     * @return string
     */
    protected function fetch()
    {
        ob_start();

        $this->render();

        return ob_get_clean();
    }

    /**
     * Render the View and output the result.
     *
     * @return void
     */
    protected function render()
    {
        // Get a local copy of the prepared data.
        $data = $this->data();

        // Extract the rendering variables from the local data copy.
        foreach ($data as $variable => $value) {
            ${$variable} = $value;
        }

        require $this->path;
    }

    /**
     * Render the View and display the result.
     *
     * @return void
     */
    public function display()
    {
        Response::sendHeaders();

        $this->render();
    }

    /**
     * Return all variables stored on local and shared data.
     *
     * @return array
     */
    public function data()
    {
        $data =& $this->data;

        // Make a local copy of the shared data.
        $shared = static::$shared;

        // All nested Views are evaluated before the main View.
        foreach ($data as $key => $value) {
            if ($value instanceof View) {
                $data[$key] = $value->fetch();
            }
        }

        // Merge the local and shared data using two steps.
        foreach (array('afterBody', 'css', 'js') as $key) {
            $value = isset($data[$key]) ? $data[$key] : '';

            if (isset($shared[$key])) {
                $value .= $shared[$key];
            }

            $data[$key] = $value;

            // Remove that key from shared data.
            unset($shared[$key]);
        }

        return array_merge($data, $shared);
    }

    /**
     * Add a view instance to the view data.
     *
     * <code>
     *     // Add a View instance to a View's data
     *     $view = View::make('foo')->nest('footer', 'Partials/Footer');
     *
     *     // Equivalent functionality using the "with" method
     *     $view = View::make('foo')->with('footer', View::make('Partials/Footer'));
     * </code>
     *
     * @param  string  $key
     * @param  string  $view
     * @param  array   $data
     * @param  string|null  $module
     * @return View
     */
    public function nest($key, $view, array $data = array(), $module = null)
    {
        return $this->with($key, View::make($view, $data, $module));
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
     * Add a key / value pair to the shared View data.
     *
     * Shared View data is accessible to every View created by the application.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public static function share($key, $value)
    {
        static::$shared[$key] = $value;
    }

    /**
     * Implementation of the ArrayAccess offsetExists method.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Implementation of the ArrayAccess offsetGet method.
     */
    public function offsetGet($offset)
    {
        if (isset($this[$offset])) return $this->data[$offset];
    }

    /**
      * Implementation of the ArrayAccess offsetSet method.
      */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * Implementation of the ArrayAccess offsetUnset method.
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
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
        // The 'fetch' and 'render' Methods are protected; expose them.
        switch ($method) {
            case 'fetch':
            case 'render':
                return call_user_func_array(array($this, $method), $params);

            default:
                break;
        }

        // Add the support for the dynamic with* Methods.
        if (strpos($method, 'with') === 0) {
            $name = lcfirst(substr($method, 4));

            return $this->with($name, array_shift($params));
        }
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * This method handles calls to dynamic with helpers.
     */
    public static function __callStatic($method, $params)
    {
        // Process the compat Methods associated to Headers management.
        switch ($method) {
            case 'addHeader':
            case 'addHeaders':
            case 'sendHeaders':
                return call_user_func_array(array(Response::class, $method), $params);

            default:
                break;
        }

        // Flag for sending, or not, the Headers, default being true.
        $withHeaders = true;

        // The called Class name for getting an instance.
        $className = static::class;

        // Prepare the required information.
        if ($method == 'render') {
            if (count($params) == 4) {
                // There is a withHeaders parameter.
                $withHeaders = array_pop($params);
            }
        } else if ($method == 'renderTemplate') {
            $className = Template::class;
        } else if ($method != 'fetch') {
            return null;
        }

        // Create a View instance, using the given classMethod and parameters.
        $object = call_user_func_array(array($className, 'make'), $params);

        if ($method == 'fetch') {
            // Render the object and return the captured output.
            return $object->fetch();
        } else if ($withHeaders) {
            // Render the object with sending the Headers first.
            return $object->display();
        }

        // Render the View object.
        return $object->render();
    }

    /**
     * Compat Layer - Render a Module View file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        echo '<p>Please use <b>View::render()</b> instead of <b>View::renderModule()</b></p>';

        if (($error !== false) && ! isset($data['error'])) {
            // Adjust the $error parameter handling, injecting it into $data.
            $data['error'] = $error;
        }

        if (preg_match('#^(.+)/Views/(.*)$#i', $path, $matches)) {
            // Render the Module's View using the standard way.
            $object = call_user_func(array(static::class, 'make'), $matches[2], $data, $matches[1]);

            $object->display();
        }
    }
}
