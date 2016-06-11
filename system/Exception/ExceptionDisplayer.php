<?php
/**
 * PlainDisplayer - Implements a plain displayer for the Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Exception\ExceptionDisplayerInterface;

use Symfony\Component\HttpFoundation\Response;

use Exception;


class ExceptionDisplayer implements ExceptionDisplayerInterface
{
    /**
     * Display the given exception to the user.
     *
     * @param  string  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function display(Exception $exception, $debug)
     {
        $viewPath = dirname(__FILE__) .DS .'Views' .DS .'Plain.php';

        if($debug) {
            $message = $e->getMessage().' in '.$e->getFile().':'.$e->getLine();

            $message = '<p>Error in exception handler: ' .$message .'</p>'
        } else {
            $message = '';
        }

        // Start rendering.
        ob_start();

        require $viewPath;

        $message = ob_get_clean();

        // Create a Response and return it.
        return new Response($message, 500);
     }
}
