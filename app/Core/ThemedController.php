<?php
/**
 * ThemedController - Base Class for all Themed Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Core;

use Nova\Core\View;
use Nova\Core\Controller;

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

    protected function beforeFlight()
    {
        // Do some processing there and stop the Flight, if is the case.
        // The available information on this method are:
        // className, called method and parameters; optionally, the module name

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        if(($result === false) || ! $this->autoRender) {
            // Errors in called Method or isn't wanted the auto-Rendering; stop the Flight.
            return false;
        }

        if(($result === true) || is_null($result)) {
            $data =& $this->data();

            if($this->useLayout) {
                $content = View::make($this->method())
                    ->loadData($data)
                    ->fetch();

                View::layout($this->layout())
                    ->loadData($data)
                    ->withContent($content)
                    ->display();

                // Stop the Flight.
                return false;
            }

            View::make($this->method())
                ->loadData($data)
                ->display();

            // Stop the Flight.
            return false;
        }
        else if($result instanceof View) {
            View::layout($this->layout())
                ->loadView($result)
                ->display();

            // Stop the Flight.
            return false;
        }

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

}
