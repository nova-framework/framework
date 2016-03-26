<?php
/**
 * Response - Manage the HTTP responses.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Helpers;

/**
 * Class Response
 *
 * @codeCoverageIgnore
 * @package Nova\Net
 */
class Response
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * @var array A listing of HTTP status codes
     * @author http://coreymaynard.com/blog/creating-a-restful-api-with-php/
     */
    public static $status = array(
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
     * Sets a status code by taking the number and automatically adding the status text
     *
     * @link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     * @param int $code
     */
    public static function addStatus($code)
    {
        if (! isset(self::$status[$code])) {
            return;
        }

        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        self::addHeader("$httpProtocol $code " . self::$status[$code]);
    }

    /**
     * Add HTTP header to headers array.
     *
     * @param  string  $header HTTP header text
     */
    public static function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add an array with headers to the view.
     *
     * @param array $headers
     */
    public static function addHeaders(array $headers = array())
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

    /**
     * Serve file
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

        //
        // Collect the current file information.

        $finfo = \finfo_open(FILEINFO_MIME_TYPE); // Return mime type ala mimetype extension

        $contentType = \finfo_file($finfo, $filePath);

        \finfo_close($finfo);

        // There is a bug with finfo_file();
        // https://bugs.php.net/bug.php?id=53035
        //
        // Hard coding the correct mime types for presently needed file extensions
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

        //
        // Prepare and send the headers with browser-side caching support.

        // Get the last-modified-date of this very file
        $lastModified = filemtime($filePath);

        // Get the HTTP_IF_MODIFIED_SINCE header if set
        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;

        // Firstly, we finalize the output buffering.
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' .$contentType);
        header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expires).' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
        //header('Etag: '.$etagFile);
        header('Cache-Control: max-age='.$expires);

        // Check if page has changed. If not, send 304 and exit
        if (@strtotime($ifModifiedSince) == $lastModified) {
            header("$httpProtocol 304 Not Modified");

            return true;
        }

        //
        // Send the current file.

        header("$httpProtocol 200 OK");
        header('Content-Length: ' .filesize($filePath));

        // Send the current file content.
        readfile($filePath);

        return true;
    }
}
