<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
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
     * The Enhanced View begins there.
     */
    protected $path = null;

    protected $data = array();
    protected $isJson = false;

    /**
     * Constructor
     */
    public function __construct($param, $isJson = false)
    {
        if(! $isJson) {
            $this->path = $param;
        }
        else {
            $this->isJson = true;
            $this->data = $param;
        }
    }

    public static function make($view)
    {
        $filePath = self::getFilePath($view);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException("File not found for the View: " .$filePath);
        }

        return new View($filePath);
    }

    public static function layout($layout = null)
    {
        $filePath = self::getFilePath($layout, true);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException("File not found for the Layout: " .$layout);
        }

        self::addHeader('Content-Type: text/html; charset=UTF-8');

        return new View($filePath);
    }

    public static function json($data)
    {
        if (is_array($data)) {
            self::addHeader('Content-Type: application/json');

            return new View($data, true);
        }

        throw new \UnexpectedValueException("Unexpected parameter on View::json");
    }

    public function fetch()
    {
        if ($this->isJson) {
            return json_encode($this->data);
        }

        foreach($this->data as $name => $value) {
            ${$name} = $value;
        }

        ob_start();

        require $this->path;

        return ob_get_clean();
    }

    public function display()
    {
        self::sendHeaders();

        echo $this->fetch();
    }

    public function with($key, $value = null)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    
    private static function getFilePath($path, $isLayout = false)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        if($isLayout) {
            $path = $path ? $path : $instance->layout();

            $template = $instance->template();

            $viewPath = APPPATH.str_replace('/', DS, "Templates/".$template.'/Layouts/');
        }
        else if ($path[0] === '/') {
            $viewPath = APPPATH."Views";
        }
        else {
            $viewPath = $instance->viewsPath();
        }

        return $viewPath.$path.'.php';
    }

    public function __call($method, $params)
    {
        if (! str_starts_with($method, 'with'))
        {
            throw new \BadMethodCallException("Method View::$method() does not exist!");
        }

        $variable = Inflector::tableize(substr($method, 4));

        return $this->with($variable, array_shift($params));
    }

    /*
     * The Enhanced View ends there.
     */

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  array  $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        if ($path[0] === '/') {
            $viewPath = APPPATH."Views";
        }
        else {
            // Get the Controller instance.
            $instance =& get_instance();

            $viewPath = $instance->viewsPath();
        }

        self::sendHeaders();

        require $viewPath.str_replace('/', DS, $path).".php";
    }

    /**
     * Include template file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($module, $path, $data = false, $error = false)
    {
        $viewPath = APPPATH.str_replace('/', DS, "Modules/".$module.'/Views/');

        self::sendHeaders();

        require $viewpath.str_replace('/', DS, $path).".php";
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
        self::sendHeaders();

        require WEBPATH."templates".DS.$custom.DS.str_replace('/', DS, $path).".php";
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
