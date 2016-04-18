<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @version 3
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
     * @var array Array of shared data
     */
    private static $shared = array();

    /**
     * @var bool Flag for the Hooks loading
     */
    private static $hooksLoaded = false;

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
        self::render($path, $data, $module, false);

        // Return the captured output.
        return ob_get_clean();
    }

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  string|false  $module module name or false
     * @param  bool   $withHeaders send or not the stored Headers
     */
    public static function render($path, $data = false, $module = false, $withHeaders = true)
    {
        // Pass data to check and store it.
        $data = self::prepareData($data);

        // Prepare the (relative) file path according with Module parameter presence.
        if ($module !== false) {
            $filePath = str_replace('/', DS, "Modules/$module/Views/$path.php");
        } else {
            $filePath = str_replace('/', DS, "Views/$path.php");
        }

        // Prepare the rendering variables.
        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        // Render the View.
        if ($withHeaders) {
            self::sendHeaders();
        }

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
        if (($error !== false) && ! isset($data['error'])) {
            // Adjust the $error parameter handling, injecting it into $data.
            $data['error'] = $error;
        }

        if (preg_match('#^(.+)/Views/(.*)$#i', $path, $matches)) {
            // Render the Module's View using the standard 'render' way.
            self::render($matches[2], $data, $matches[1]);
        }
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
        // Pass data to check and store it.
        $data = self::prepareData($data);

        // Prepare the (relative) file path.
        $filePath = str_replace('/', DS, "Templates/$custom/$path.php");

        // Prepare the rendering variables.
        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        // Render the Template.
        self::sendHeaders();

        require APPDIR .$filePath;
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
     * Place hook calls into the relevant data array call
     * @param  array $data
     * @return array $data
     */
    private static function prepareData($data)
    {
        // Run the associated Hooks if they aren't already loaded.
        if(! static::$hooksLoaded) {
            $hooks = Hooks::get();

            static::$shared['afterBody'] = $hooks->run('afterBody', static::$shared['afterBody']);
            static::$shared['css']       = $hooks->run('css', static::$shared['css']);
            static::$shared['js']        = $hooks->run('js', static::$shared['js']);

            // Mark the Hooks being loaded.
            static::$hooksLoaded = true;
        }

        // Ensure that the current data is an array.
        $data = is_array($data) ? $data : array();

        // Merge the current data and the shared one.
        $data = array_merge($data, static::$shared);

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
