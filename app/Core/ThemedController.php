<?php
/**
 * ThemedController - Base Class for all Themed Controllers.
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

    // Store the Controller's variables.
    protected $data = array();


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function beforeFlight()
    {
        // Do some processing there and stop the Flight, if is the case.
        // The available information on this method are:
        // className, called method and parameters; optionally, the module name

        // Leave to the parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    public function afterFlight($result)
    {
        if($result instanceof View) {
            View::layout($this->layout())
                ->loadView($result, true)
                ->display();

            // Stop the Flight.
            return false;
        }
        else if(is_null($result)) {
            $data =& $this->data();

            if(! empty($data)) {
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
        }

        // Leave to the parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    public function data($name = null)
    {
        if(is_null($name)) {
            return $this->data;
        }
        else if(isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            if (is_array($value)) {
                $data = array_combine($name, $value);
            }
            else {
                $data = $name;
            }
        }
        else {
            $data = array($name => $value);
        }

        $this->data = $data + $this->data;
    }

    public function title($title)
    {
        $data = array('title' => $title);

        $this->data = $data + $this->data;
    }

}
