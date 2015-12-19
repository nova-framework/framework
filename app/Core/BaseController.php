<?php
/**
 * ClassicController - Base Class for all App Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 18th, 2015
 */

namespace App\Core;

use Smvc\Core\View;
use Smvc\Core\Controller;

/**
 * Simple themed controller showing the typical usage of the Flight Control method.
 */
class BaseController extends Controller
{
    // Store the Controller's variables.
    protected $data = array();

    protected $useLayout = false;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Leave to parent's method the Flight decisions.
        return parent::afterFlight();
    }

    protected function renderResult($result)
    {
        if(is_array($result)) {
            View::addHeader('Content-Type: application/json');

            $result = json_encode($result);
        }
        else if(is_string($result)) {
            View::addHeader('Content-Type: text/html; charset=UTF-8');
        }
        else {
            parent::renderResult($result);

            return;
        }

        // Output the result.
        View::sendHeaders();

        echo $result;
    }

    protected function useLayout($value = null)
    {
        if(is_null($value)) {
            return $this->useLayout;
        }

        $this->useLayout = $value;
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

    protected function set($name, $value = null)
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

    protected function title($title)
    {
        $data = array('title' => $title);

        $this->data = $data + $this->data;

        // Activate the Rendering on Layout.
        $this->useLayout = true;
    }

}
