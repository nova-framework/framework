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
        if($debug) {
            $message = 'Error in exception handler: ' .$e->getMessage().' in '.$e->getFile().':'.$e->getLine();
        } else {
            $message = '';
        }

        // Start rendering.
        ob_start();

        require dirname(__FILE__) .DS .'Resources' .DS .'plain.php';

        $message = ob_get_clean();

        return new Response($message, 500);
     }
}
