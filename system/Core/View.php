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
    /*
     * The View's internal stored variables.
     */
    protected $path = null;

    protected $data = array();
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

    public function __call($method, $params)
    {
        if (strpos($method, 'with') !== 0) {
            throw new \BadMethodCallException(__d('system', 'Invalid method called: View::{0}', $method));
        }

        $varname = Inflector::tableize(substr($method, 4));

        return $this->with($varname, array_shift($params));
    }

    /**
     * Make view
     * @param $view
     * @return View
     * @throws \UnexpectedValueException
     */
    public static function make($view)
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
    public static function layout($layout = null)
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
        if ($this->json) {
            return json_encode($this->data);
        }

        // Prepare the rendering variables.
        foreach ($this->data as $name => $value) {
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

    public function with($key, $value = null)
    {
        $this->data[$key] = $value;

        return $this;
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

    public function loadData($data)
    {
        if ($data instanceof View) {
            $this->data = array_merge($this->data, $data->data());
        } else {
            if (! is_array($data)) {
                throw new \UnexpectedValueException(__d('system', 'Unexpected parameter'));
            }

            $this->data = array_merge($this->data, $data);
        }

        return $this;
    }

    public function loadView($view)
    {
        if ($view instanceof View) {
            $this->data = $view->data();

            return $this->with('content', $view->fetch());
        }

        throw new \UnexpectedValueException(__d('system', 'Unknown parameter'));
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

        return APPPATH.'Templates'.DS.$template.DS;
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
