<?php
/**
 * PlainDisplayer - Implements a plain displayer for the Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Whoops\Handler\Handler as WhoopsHandler;
use Whoops\Util\Misc;


class PlainDisplayer extends WhoopsHandler
{
    /**
     * @return int
     */
    public function handle()
    {
        if (Misc::isAjaxRequest()) {
            if (Misc::canSendHeaders()) {
                header('Content-Type: application/json');
            }

            $response = json_encode(array('error' => 'Whoops! There was an error.'));
        } else {
            $response = file_get_contents(__DIR__.'/Resources/plain.html');
        }

        echo $response;

        return WhoopsHandler::QUIT;
    }
}
