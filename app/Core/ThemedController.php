<?php
/**
 * ThemedController - Base Class for all themed Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Core;

use Smvc\Core\View;
use Smvc\Core\Controller;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class ThemedController extends Controller
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function afterFlight($result)
    {
        $title = __('Welcome');

        if($result instanceof View) {
            View::addHeader('Content-Type: text/html; charset=UTF-8');

            $title = $result->get('title');

            $content = $result->fetch();
        }
        else if(is_array($result)) {
            View::addHeader('Content-Type: application/json');

            $content = json_encode($result);
        }
        else if(is_string($result)) {
            View::addHeader('Content-Type: text/html; charset=UTF-8');
        }
        else if(is_integer($result)) {
            // Just to see '0' on webpage and nothing more.
            $content = sprintf('%d', $result);
        }
        else {
            $content = $result;
        }

        View::layout($this->layout())
            ->withTitle($title)
            ->withContent($content)
            ->display();

        // Return false to stop the Flight.
        return false;
    }

}
