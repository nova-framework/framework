<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */


namespace Nova\Core;


/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    // The Controller's instance.
    private static $instance;

    // The Controller's variables.
    protected $data = array();

    // Module where the Controller is located.
    protected $module = null;
    //
    protected $params = array();

    // Current called Method
    protected $method;

    protected $className;
    protected $viewsPath;

    // Theming support.
    protected $template = 'Default';
    protected $layout = 'default';

    protected $autoRender = true;
    protected $useLayout = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &getInstance()
    {
        return self::$instance;
    }

    public function setInstance()
    {
        self::$instance =& $this;
    }

    public function initialize($className, $method, $params = array())
    {
        $this->className = $className;

        $this->method = $method;
        $this->params = $params;

        // Prepare the Views Path using the Controller's full Name including its namespace.
        $classPath = str_replace('\\', '/', $className);

        // First, check on the App path.
        if (preg_match('#^App/Controllers/(.*)$#i', $classPath, $matches)) {
            $viewsPath = str_replace('/', DS, 'Views/' . $matches[1]);
        } // Secondly, check on the Modules path.
        else if (preg_match('#^App/Modules/(.+)/Controllers/(.*)$#i', $classPath, $matches)) {
            $this->module = $matches[1];

            // The View paths are in Module sub-directories.
            $viewsPath = str_replace('/', DS, 'Modules/' . $matches[1] . '/Views/' . $matches[2]);
        } else {
            throw \Exception('Unknown Views Path for the Class: ' . $className);
        }

        $this->viewsPath = APPPATH . $viewsPath . DS;
    }

    public function execute()
    {
        if ($this->beforeFlight() === false) {
            // Is wanted to stop the Flight.
            return false;
        }

        // Execute the Controller's Method with the given arguments.
        $result = call_user_func_array(array($this, $this->method()), $this->params());

        if (($this->afterFlight($result) === false) || !$this->autoRender) {
            // Is wanted to stop the Flight or there is no auto-rendering.
            return true;
        }

        $this->renderResult($result);

        return true;
    }

    protected function beforeFlight()
    {
        return true;
    }

    public function method()
    {
        return $this->method;
    }

    public function params()
    {
        return $this->params;
    }

    protected function afterFlight($result)
    {
        return true;
    }

    protected function renderResult($result)
    {
        if ($result instanceof View) {
            $result->display();

            return;
        }

        if (is_array($result)) {
            View::addHeader('Content-Type: application/json');

            $result = json_encode($result);
        } else if (is_string($result)) {
            View::addHeader('Content-Type: text/html; charset=UTF-8');
        } else {
            return;
        }

        // Output the result.
        View::sendHeaders();

        echo $result;
    }

    public function data($name = null)
    {
        if (is_null($name)) {
            return $this->data;
        } else if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    public function module()
    {
        return $this->module;
    }

    public function viewsPath()
    {
        return $this->viewsPath;
    }

    // Some getters.

    public function template()
    {
        return $this->template;
    }

    public function layout()
    {
        return $this->layout;
    }

    protected function autoRender($value = null)
    {
        if (is_null($value)) {
            return $this->autoRender;
        }

        $this->autoRender = $value;
    }

    protected function useLayout($value = null)
    {
        if (is_null($value)) {
            return $this->useLayout;
        }

        $this->useLayout = $value;
    }

    protected function set($name, $value = null)
    {
        if (is_array($name)) {
            if (is_array($value)) {
                $data = array_combine($name, $value);
            } else {
                $data = $name;
            }
        } else {
            $data = array($name => $value);
        }

        $this->data = $data + $this->data;
    }

    protected function title($title)
    {
        $data = array('title' => $title);

        $this->data = $data + $this->data;

        // Activate the Rendering on Layout.
        $this->useLayout = true;
    }

}
