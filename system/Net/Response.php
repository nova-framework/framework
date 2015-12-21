<?php
/**
 * Response - Manage the HTTP responses.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 21th, 2015
 */

namespace Nova\Net;


class Response
{

    public static function serveFile($filePath)
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        $expires = 60 * 60 * 24 * 365; // Cache for one year

        if (! file_exists($filePath)) {
            header("$httpProtocol 404 Not Found");

            return false;
        }
        else if (! is_readable($filePath)) {
            header("$httpProtocol 403 Forbidden");

            return false;
        }
        //
        // Collect the current file information.

        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Return mime type ala mimetype extension

        $contentType = finfo_file($finfo, $filePath);

        finfo_close($finfo);

        // There is a bug with finfo_file();
        // https://bugs.php.net/bug.php?id=53035
        //
        // Hard coding the correct mime types for presently needed file extensions
        switch($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
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

        // Firstly, we finalize the output buffering.
        if (ob_get_level()) ob_end_clean();

        header("$httpProtocol 200 OK");
        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' .$contentType);
        header('Content-Length: ' .filesize($filePath));

        // Send the current file content.
        readfile($filePath);

        return true;
    }

}
