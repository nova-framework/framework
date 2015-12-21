<?php
/**
 * ThemedController - Base Class for all Themed Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Core;

use Nova\Core\View;
use Nova\Core\Controller;
use Nova\Events\Manager as Events;


/**
 * Simple themed controller showing the typical usage of the Flight Control method.
 */
class ThemedController extends Controller
{
    protected $layout = 'themed';

    protected $events = null;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->events = Events::getInstance();

        // Setup the Data Entries.
        $this->data = array(
            'headerCSS' => array(),
            'headerJScript' => array(),
            'footerJScript' => array(),
            'afterBodyArea' => array(),
            'footerArea'    => array()
        );
    }

    protected function beforeFlight()
    {
        $data =& $this->data;

        $params = array(
            'controller' => $this->className,
            'method'     => $this->method,
            'params'     => $this->params,
        );

        // Broadcast the Event to all its Listeners; if they return a valid array, merge it to Data.
        $this->events->trigger('Nova.Routing.BeforeFlight', $params, function($result) use (&$data) {
            if(! is_array($result)) {
                return;
            }

            foreach($result as $key => $value) {
                switch($key) {
                    case 'headerCSS':
                    case 'headerJScript':
                    case 'footerJScript':
                    case 'afterBodyArea':
                    case 'footerArea':
                        break;
                    default:
                        continue;
                }

                if(is_array($value)) {
                    $data[$key] = array_merge($data[$key], $value);
                }
                else if(is_string($value)) {
                    $data[$key] = $value;
                }
            }
        });

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
