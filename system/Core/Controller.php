<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */


namespace Smvc\Core;

use Smvc\Core\View;

/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    // The Controller's instance.
    private static $instance;

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
    protected $layout   = 'default';

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

    public function initialize($className, $method, $params)
    {
        $this->className = $className;

        $this->method = $method;
        $this->params = $params;

        // Prepare the Views Path using the Controller's full Name including its namespace.
        $classPath = str_replace('\\', '/', $className);

        // First, check on the App path.
        if(preg_match('#^App/Controllers/(.*)$#i', $classPath, $matches)) {
            $viewsPath = str_replace('/', DS, 'Views/'.$matches[1]);
        }
        // Secondly, check on the Modules path.
        else if(preg_match('#^App/Modules/(.+)/Controllers/(.*)$#i', $classPath, $matches)) {
            $this->module = $matches[1];

            // The View paths are in Module sub-directories.
            $viewsPath = str_replace('/', DS, 'Modules/'.$matches[1].'/Views/'.$matches[2]);
        }
        else {
            throw \Exception('Unknown Views Path for the Class: '.$className);
        }

        $this->viewsPath = APPPATH .$viewsPath .DS;
    }

    public function beforeFlight()
    {
        return true;
    }

    public function afterFlight($result)
    {
        return true;
    }

    public function execute()
    {
        if($this->beforeFlight() === false) {
            // Is wanted to stop the Flight.
            return false;
        }

        // Execute the Controller's Method with the given arguments.
        $result = call_user_func_array(array($this, $this->method()), $this->params());

        if($this->afterFlight($result) === false) {
            // Is wanted to stop the Flight.
            return true;
        }

        $this->renderResult($result);

        return true;
    }

    protected function renderResult($result)
    {
        if($result instanceof View) {
            $result->display();
        }
    }

    // Some getters.

    public function module()
    {
        return $this->module;
    }

    public function method()
    {
        return $this->method;
    }

    public function params()
    {
        return $this->params;
    }

    public function viewsPath()
    {
        return $this->viewsPath;
    }

    public function template($value = null)
    {
        if(is_null($value)) {
            return $this->template;
        }

        $this->template = $value;
    }

    public function layout($value = null)
    {
        if(is_null($value)) {
            return $this->layout;
        }

        $this->layout = $value;
    }

}
