<?php
/**
 * Response - Manage the HTTP responses.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Template;
use Core\View;


/**
 * Class Response
 */
class Response
{
    /**
     * @var mixed The content of the Response.
     */
    protected $content = '';

    /**
     * @var int HTTP Status
     */
    protected $status = 200;

    /**
     * @var array Array of HTTP headers
     */
    protected $headers = array();

    /**
     * @var array Array of legacy HTTP headers
     */
    protected static $legacyHeaders = array();

    /**
     * @var array A listing of HTTP status codes
     * @author http://coreymaynard.com/blog/creating-a-restful-api-with-php/
     */
    public static $statuses = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     * Create a new Response instance.
     *
     * @param  mixed  $content
     * @param  int    $status
     * @param  array  $headers
     * @return void
     */
    protected function __construct($content = '', $status = 200, array $headers = array())
    {
        if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * Create a new Response instance.
     *
     * <code>
     *      // Create a Response instance with string content.
     *      return Response::make(json_encode($user));
     *
     *      // Create a Response instance with a given status.
     *      return Response::make('Not Found', 404);
     *
     *      // Create a Response with some custom headers.
     *      return Response::make(json_encode($user), 200, array('header' => 'value'));
     * </code>
     *
     * @param  mixed     $content
     * @param  int       $status
     * @param  array     $headers
     * @return Response
     */
    public static function make($content, $status = 200, array $headers = array())
    {
        return new static($content, $status, $headers);
    }

    /**
     * Create a new Response instance containing a View.
     *
     * <code>
     *      // Create a response instance with a View.
     *      return Response::view('Home/Index');
     *
     *      // Create a response instance with a View and Data.
     *      return Response::view('Home/Index', array('name' => 'Taylor'));
     * </code>
     *
     * @param  string    $view
     * @param  array     $data
     * @return Response
     */
    public static function view($view, array $data = array())
    {
        $view = View::make($view, $data);

        return new static($view);
    }

    /**
     * Create a new JSON Response.
     *
     * <code>
     *       // Create a response instance with JSON.
     *       return Response::json($data, 200, array('header' => 'value'));
     * </code>
     *
     * @param  mixed     $data
     * @param  int       $status
     * @param  array     $headers
     * @param  int       $jsonOptions
     * @return Response
     */
    public static function json($data, $status = 200, $headers = array(), $jsonOptions = 0)
    {
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        return new static(json_encode($data, $jsonOptions), $status, $headers);
    }

    /**
     * Create a new JSONP response.
     *
     * <code>
     *      // Create a response instance with JSONP.
     *      return Response::jsonp('myFunctionCall', $data, 200, array('header' => 'value'));
     * </code>
     *
     * @param  mixed     $data
     * @param  int       $status
     * @param  array     $headers
     * @return Response
     */
    public static function jsonp($callback, $data, $status = 200, array $headers = array())
    {
        $headers['Content-Type'] = 'application/javascript; charset=utf-8';

        return new static($callback .'(' .json_encode($data) .');', $status, $headers);
    }

    /**
     * Create a new Error Response instance.
     *
     * The Response Status code will be set using the specified code.
     *
     * The specified error should match a View in your Views/Error directory.
     *
     * <code>
     *      // Create a 404 response.
     *      return Response::error('404');
     *
     *      // Create a 404 response with data.
     *      return Response::error('404', array('message' => 'Not Found'));
     * </code>
     *
     * @param  int       $code
     * @param  array     $data
     * @return Response
     */
    public static function error($code, array $data = array())
    {
        $view = Template::make('default')
            ->shares('title', 'Error ' .$code)
            ->nest('content', 'Error/' .$code, $data);

        return new static($view, $code);
    }

    /**
     * Prepare a Response from the given value.
     *
     * @param  mixed     $response
     * @return Response
     */
    public static function prepare($response)
    {
        if (! $response instanceof Response) {
            $response = new static($response);
        }

        return $response;
    }

    /**
     * Send the headers and content of the Response to the web-browser.
     *
     * @return void
     */
    public function send()
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        // Send the HTTP Status and Headers.

        if (! headers_sent()) {
            $status = $this->status();

            // Send the HTTP Status Header.
            header("$httpProtocol $status " . self::$statuses[$status]);

            // Send the rest of the HTTP Headers.
            foreach ($this->headers as $name => $value) {
                header("$name: $value", true);
            }
        }

        // Send the stringified Content.

        echo $this->render();
    }

    /**
     * Convert the content of the Response to a string and return it.
     *
     * @return string
     */
    public function render()
    {
        if (str_object($this->content)) {
            // If the content is a stringable object, we'll go ahead and call the toString method.
            $this->content = $this->content->__toString();
        } else {
            // Otherwise we'll just cast to string.
            $this->content = (string) $this->content;
        }

        return trim($this->content);
    }

    /**
     * Add a header to the array of response headers.
     *
     * @param  string    $name
     * @param  string    $value
     * @return Response
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Get the Response headers.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Get / set the Response Status code.
     *
     * @param  int    $status
     * @return mixed
     */
    public function status($status = null)
    {
        if (is_null($status)) {
            return $this->status;
        } else if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Return the Response's Content.
     *
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Render the response when cast to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Serve a File.
     *
     * @param string $filePath
     * @return bool
     */
    public static function serveFile($filePath)
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        $expires = 60 * 60 * 24 * 365; // Cache for one year

        if (! file_exists($filePath)) {
            header("$httpProtocol 404 Not Found");

            return false;
        } else if (! is_readable($filePath)) {
            header("$httpProtocol 403 Forbidden");

            return false;
        }

        // Collect the current file information.

        $finfo = \finfo_open(FILEINFO_MIME_TYPE); // Return mime type a la mimetype extension

        $contentType = \finfo_file($finfo, $filePath);

        \finfo_close($finfo);

        // There is a bug with finfo_file();
        // https://bugs.php.net/bug.php?id=53035
        //
        // Hard coding the correct mime types for presently needed file extensions.
        switch ($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                break;
        }

        // Prepare and send the headers with browser-side caching support.

        // Get the last-modified-date of this very file.
        $lastModified = filemtime($filePath);

        // Get the HTTP_IF_MODIFIED_SINCE header if set.
        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;

        // Firstly, we finalize the output buffering.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' .$contentType);
        header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expires).' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
        // header('Etag: '.$etagFile);
        header('Cache-Control: max-age='.$expires);

        // Check if the page has changed. If not, send 304 and exit.
        if (@strtotime($ifModifiedSince) == $lastModified) {
            header("$httpProtocol 304 Not Modified");

            return true;
        }

        // Send the current file.

        header("$httpProtocol 200 OK");
        header('Content-Length: ' .filesize($filePath));

        // Send the current file content.
        readfile($filePath);

        return true;
    }

    //--------------------------------------------------------------------
    // Legacy Headers API
    //--------------------------------------------------------------------

    /**
     * Add the HTTP header to the headers array.
     *
     * @param  string  $header HTTP header text
     */
    public static function addHeader($header)
    {
        self::$legacyHeaders[] = $header;
    }

    /**
     * Add an array with headers to the view.
     *
     * @param array $headers
     */
    public static function addHeaders(array $headers)
    {
        if(empty($headers)) {
            return;
        }

        self::$legacyHeaders = array_merge(self::$legacyHeaders, $headers);
    }

    /**
     * Send headers.
     */
    public static function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        foreach (self::$legacyHeaders as $header) {
            header($header, true);
        }
    }
}
