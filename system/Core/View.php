<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @version 2.2
 */

namespace Core;

use Helpers\Hooks;

/**
 * View class to load template and views files.
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * Render the View file and return the result.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  string|false  $module module name or false
     */
    public static function fetch($path, $data = false, $module = false)
    {
        // Start the output buffering.
        ob_start();

        // Render the requested View.
        self::render($path, $data, $module);

        // Return the captured output.
        return ob_get_clean();
    }

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  string|false  $module module name or false
     */
    public static function render($path, $data = false, $module = false)
    {
        self::sendHeaders();

        // Pass data to check and store it.
        $data = self::prepareData($data);

        // Prepare the (relative) file path according with Module parameter presence.
        if($module !== false) {
            $filePath = str_replace('/', DS, "Modules/$module/Views/$path.php");
        } else {
            $filePath = str_replace('/', DS, "Views/$path.php");
        }

        // Prepare the rendering variables.
        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        // Render the View.
        require APPDIR .$filePath;
    }

    /**
     * Include template file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        self::sendHeaders();

        // Pass data to check and store it.
        $data = self::prepareData($data);

        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        require APPDIR."Modules/$path.php";
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

        // Pass data to check and store it.
        $data = self::prepareData($data);

        // Prepare the (relative) file path.
        $filePath = str_replace('/', DS, "Templates/$custom/$path.php");

        // Prepare the rendering variables.
        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        require APPDIR .$filePath;
    }

    /**
     * place hook calls into the relevant data array call
     * @param  array $data
     * @return array $data
     */
    public static function prepareData($data)
    {
        $data = ($data !== false) ? $data : array();

        // Run the associated Hooks.
        $hooks = Hooks::get();

        $data['afterBody'] = $hooks->run('afterBody', $data['afterBody']);
        $data['css']       = $hooks->run('css', $data['css']);
        $data['js']        = $hooks->run('js', $data['js']);

        return $data;
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
        if (! headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
    }
}
