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

use Symfony\Component\HttpFoundation\JsonResponse;

use Exception;


class JsonExceptionDisplayer implements ExceptionDisplayerInterface
{
    /**
     * Display the given exception to the User.
     *
     * @param  string  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function display(Exception $exception, $debug = true)
     {
        if($debug) {
            $message = array(
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            );
        } else {
            $message = array(
                'message' => 'Error in Exception Handler',
                'file'    => '',
                'line'    => 0,
            );
        }

        // Start the View rendering.
        $data = array('error' => $message);

        // Prepare the Response and send it.
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();

        // Create a Response and return it.
        return new JsonResponse($data, $status, $headers());
     }
}
