<?php
/**
 * PlainDisplayer - Implements a plain displayer for the Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Exception\ExceptionDisplayerInterface;
use Exception\HttpExceptionInterface;

use Symfony\Component\HttpFoundation\Response;

use Exception;


class PlainDisplayer implements ExceptionDisplayerInterface
{
    /**
     * Display the given exception to the user.
     *
     * @param  string  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function display(Exception $exception)
     {
        $viewPath = dirname(__FILE__) .DS .'Resources' .DS .'Plain.php';

        // Start the View rendering.
        ob_start();

        require $viewPath;

        $message = ob_get_clean();

        // Prepare the Response and send it.
        $status = ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : 500;

        $headers = ($exception instanceof HttpExceptionInterface) ? $exception->getHeaders() : array();

        // Create a Response and return it.
        return new Response($message, $status, $headers);
     }
}
