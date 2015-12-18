<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated Dec 18, 2015
 */

namespace Smvc\Core;

use Smvc\Core\Controller;
use Smvc\Helpers\Inflector;

/**
 * View class to load template and views files.
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /*
     * The View's internal stored variables.
     */
    protected $path = null;

    protected $data = array();
    protected $json = false;

    /**
     * Constructor
     */
    public function __construct($param, $json = false)
    {
        if(! $json) {
            $this->path = $param;

            return;
        }

        if (! is_array($param)) {
            throw new \UnexpectedValueException('Parameter should be Array, on View::__construct');
        }

        $this->json = true;
        $this->data = $param;
    }

    public function __call($method, $params)
    {
        if (strpos($method, 'with') !== 0)
        {
            throw new \BadMethodCallException('Invalid method called: View::'.$method);
        }

        $varname = Inflector::tableize(substr($method, 4));

        return $this->with($varname, array_shift($params));
    }

    public static function make($view)
    {
        $filePath = self::viewPath($view);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        return new View($filePath);
    }

    public static function layout($layout = null)
    {
        $filePath = self::layoutPath($layout);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        self::addHeader('Content-Type: text/html; charset=UTF-8');

        return new View($filePath);
    }

    public static function fragment($fragment, $fromTemplate = true)
    {
        $filePath = self::fragmentPath($fragment, $fromTemplate);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        return new View($filePath);
    }

    public static function json($data)
    {
        if (! is_array($data)) {
            throw new \UnexpectedValueException('Unexpected parameter on View::json');
        }

        self::addHeader('Content-Type: application/json');

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
        foreach($this->data as $name => $value) {
            ${$name} = $value;
        }

        // Execute the rendering, then capture and return the output.
        ob_start();

        require $this->path;

        return ob_get_clean();
    }

    public function display()
    {
        if ($this->json) {
            echo json_encode($this->data);
        }

        // Prepare the rendering variables.
        foreach($this->data as $name => $value) {
            ${$name} = $value;
        }

        // Execute the rendering to output.
        self::sendHeaders();

        require $this->path;
    }

    public function with($key, $value = null)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function data($name = null)
    {
        if(is_null($name)) {
            return $this->data;
        }
        else if(isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    public function loadData($data)
    {
        if (! is_array($data)) {
            throw new \UnexpectedValueException('Unexpected parameter on View::loadData');
        }

        $this->data = $data;

        return $this;
    }

    public function loadView($view, $fetch = false)
    {
        $this->data = $view->data();

        if($fetch) {
            $this->with('content', $view->fetch());
        }

        return $this;
    }

    private static function viewPath($path)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if ($path[0] === '/') {
            // A Views "Root" path is wanted.
            $viewPath = APPPATH."Views";
        }
        else {
            $viewPath = $instance->viewsPath();
        }

        return realpath($viewPath.$path.'.php');
    }

    private static function templatePath($template = null)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        $template = $template ? $template : $instance->template();

        return APPPATH.'Templates'.DS.$template.DS;
    }

    private static function layoutPath($layout = null, $template = null)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        $layout = $layout ? $layout : $instance->layout();

        $basePath = self::templatePath($template);

        // Adjust the filePath for Layouts
        return $basePath.'Layouts'.DS.$layout.'.php';
    }

    private static function fragmentPath($fragment, $fromTemplate = true)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if($fromTemplate) {
            $basePath = self::templatePath();
        }
        else {
            $basePath = APPPATH.'Views'.DS;

            // If we are in a Module, we should adjust the basePath.
            $module = $instance->module();

            if($module) {
                // Adjust the filePath for Module.
                $basePath = APPPATH.'Modules'.DS.$module.DS;
            }
        }

        // Adjust the filePath for Fragments
        return $basePath.'Fragments'.DS.$fragment.'.php';
    }

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  array  $error array of errors
     */
    public static function render($path, $data = false, $error = false, $fetch = false)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if ($path[0] === '/') {
            // A Views "Root" Path is wanted.
            $basePath = APPPATH."Views";
        }
        else {
            $basePath = $instance->viewsPath();
        }

        $filePath = $basePath.str_replace('/', DS, $path).".php";

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if($data) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        self::sendHeaders();

        if($fetch) {
            ob_start();
        }

        require $filePath;

        if($fetch) {
            return ob_get_clean();
        }

        return false;
    }

    /**
     * Include template file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($module, $path, $data = false, $error = false, $fetch = false)
    {
        $basePath = APPPATH.str_replace('/', DS, "Modules/".$module.'/Views/');

        $filePath = $basePath.str_replace('/', DS, $path).".php";

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if($data) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        self::sendHeaders();

        if($fetch) {
            ob_start();
        }

        require $filePath;

        if($fetch) {
            return ob_get_clean();
        }

        return false;
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param  string  $layout layout name
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderLayout($layout, $content, $data = false, $custom = null)
    {
        $filePath = self::layoutPath($layout, $custom);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if($data) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        self::sendHeaders();

        require $filePath;
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = TEMPLATE)
    {
        $basePath = WEBPATH."templates".DS.$custom.DS;

        $filePath = $basePath.str_replace('/', DS, $path).".php";

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if($data) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        self::sendHeaders();

        require $filePath;
    }

    /**
     * Add HTTP header to headers array.
     *
     * @param  string  $header HTTP header text
     */
    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add an array with headers to the view.
     *
     * @param array $headers
     */
    public function addHeaders(array $headers = array())
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    /**
     * Send headers
     */
    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
    }
}
