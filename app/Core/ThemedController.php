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
 * Simple themed controller showing the typical usage of the Flight Control method.
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

    public function beforeFlight()
    {
        // Do some processing there and stop the Flight if is case.
        // Available information on this method:
        // className, called method and parameters; also the module name, if any

        // Leave to Parent's Method the Flight decision.
        return parent::beforeFlight();
    }

    public function afterFlight($result)
    {
        if($result instanceof View) {
            View::layout($this->layout())
                ->loadView($result, true)
                ->display();

            // We rendered the View in its Layout; stop the Flight.
            return false;
        }

        // Leave to Parent's Method the Flight decision.
        return parent::afterFlight($result);
    }

}
