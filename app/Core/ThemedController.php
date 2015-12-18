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
    protected $layout = 'themed';


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function afterFlight($result)
    {
        if($result instanceof View) {
            View::layout($this->layout)
                ->loadView($result, true)
                ->display();

            // We rendered the View in its Layout; stop the Flight.
            return false;
        }

        // Return true to continue the Flight.
        return true;
    }

}
