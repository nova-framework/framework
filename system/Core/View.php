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
        if(! $json) {
            $this->path = $param;

            return;
        }

        if (! is_array($param)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
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
            throw new \UnexpectedValueException('File not found: '.$filePath);
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
            throw new \UnexpectedValueException('File not found: '.$filePath);
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
            throw new \UnexpectedValueException('File not found: '.$filePath);
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
            throw new \UnexpectedValueException('Unexpected parameter');
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
        foreach($this->data as $name => $value) {
            ${$name} = $value;
        }

        // Execute the rendering, then capture and return the output.
        ob_start();

        require $this->path;

        return ob_get_clean();
    }

    public function render()
    {
        if ($this->json) {
            echo json_encode($this->data);
        }

        // Prepare the rendering variables.
        foreach($this->data as $name => $value) {
            ${$name} = $value;
        }

        // Execute the rendering to output.
        Response::sendHeaders();

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
        if($data instanceof View) {
            $this->data = array_merge($this->data, $data->data());
        }
        else {
            if (! is_array($data)) {
                throw new \UnexpectedValueException('Unexpected parameter');
            }

            $this->data = array_merge($this->data, $data);
        }

        return $this;
    }

    public function loadView($view)
    {
        if($view instanceof View) {
            $this->data = $view->data();

            return $this->with('content', $view->fetch());
        }

        throw new \UnexpectedValueException('Unknown parameter');
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
     * @param string $path path to file from views folder
     * @param array|bool $data array of data
     * @param bool $fetch
     * @return bool|string
     * @internal param array $error array of errors
     */
    public static function renderView($path, $data = false, $fetch = false)
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

        if(is_array($data)) {
            $data = $data + $instance->data();
        }
        else {
            $data = $instance->data();
        }

        if(! empty($data)) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        if($fetch) {
            ob_start();
        }
        else {
            Response::sendHeaders();
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
     * @param  string $module module
     * @param  string $path path to file from Modules folder
     * @param  array|boolean $data array or boolean of data
     * @param  array|boolean $fetch fetch data
     * @return bool|string
     */
    public static function renderModule($module, $path, $data = false, $fetch = false)
    {
        $module = Inflector::classify($module);

        // Get the Controller instance.
        $instance =& get_instance();

        if ($path[0] === '/') {
            // A Views "Root" Path is wanted.
            $basePath = APPPATH.str_replace('/', DS, "Modules/".$module.'/Views/');
        }
        else if($instance->module() == $module) {
            $basePath = $instance->viewsPath();
        }
        else {
            throw new \UnexpectedValueException('Invalid Module requested: '.$module);
        }

        $filePath = $basePath.str_replace('/', DS, $path).".php";

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if(is_array($data)) {
            $data = $data + $instance->data();
        }
        else {
            $data = $instance->data();
        }

        if(! empty($data)) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        if($fetch) {
            ob_start();
        }
        else {
            Response::sendHeaders();
        }

        require $filePath;

        if($fetch) {
            return ob_get_clean();
        }

        return false;
    }

    public static function renderPage($view, $data = false, $layout = null, $custom = null)
    {
        // Render the Page using the Content fetching and the Layout.
        $content = self::renderView($view, $data, true);

        self::renderLayout($layout, $content, $data, $custom);
    }

    public static function renderLayout($layout, $content, $data = false, $custom = null)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        $filePath = self::layoutPath($layout, $custom);

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if(is_array($data)) {
            $data = $data + $instance->data();
        }
        else {
            $data = $instance->data();
        }

        if(! empty($data)) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        Response::sendHeaders();

        require $filePath;
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param  string  $path  path to file from views folder
     * @param  array|boolean   $data  array of data
     * @param  string  $custom path to template folder
     * @throws \UnexpectedValueException
     */
    public static function renderTemplate($path, $data = false, $custom = TEMPLATE)
    {
        // Get the Controller instance.
        $instance =& get_instance();

        $custom = Inflector::classify($custom);

        $basePath = self::templatePath($custom)."Layouts".DS.'partials'.DS;

        $filePath = $basePath.str_replace('/', DS, $path).".php";

        if (! is_readable($filePath)) {
            throw new \UnexpectedValueException('File not found: '.$filePath);
        }

        if(is_array($data)) {
            $data = $data + $instance->data();
        }
        else {
            $data = $instance->data();
        }

        if(! empty($data)) {
            // Extract the rendering variables.
            foreach($data as $name => $value) {
                ${$name} = $value;
            }
        }

        Response::sendHeaders();

        require $filePath;
    }

}
