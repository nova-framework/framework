<?php
/**
 * BaseController - Base Class for all App Controllers who use Templates.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 18th, 2015
 */

namespace App\Core;

use Nova\Core\View;
use Nova\Core\Controller;
use Nova\Events\Manager as Events;

/**
 * Simple themed controller showing the typical usage of the Flight Control method.
 */
class BaseController extends Controller
{
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
            'headerMetaData' => array(),
            'headerCSSheets'  => array(),
            'headerJScripts'  => array(),
            'footerJScripts'  => array(),
        );
    }

    /**
     * Before 'Flight' action
     * @return bool
     */
    protected function beforeFlight()
    {
        $data =& $this->data;

        $params = array(
            'controller' => $this->className,
            'method'     => $this->method,
            'params'     => $this->params,
            'context'    => $this->module ? $this->module : 'App'
        );

        // Broadcast the Event to all its Listeners; if they return a valid array, merge it to Data.
        $this->events->trigger('App.Core.BaseController.BeforeFlight', $params, function ($result) use (&$data) {
            if (! is_array($result)) {
                return;
            }

            foreach ($result as $key => $value) {
                switch ($key) {
                    case 'headerMetaData':
                    case 'headerCSSheets':
                    case 'headerJScripts':
                    case 'footerJScripts':
                        if (! is_array($value)) {
                            continue;
                        }

                        break;
                    default:
                        continue;
                }

                if (! empty($value)) {
                    $data[$key] = array_merge($data[$key], $value);
                }
            }
        });

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    /**
     * After 'flight' action
     *
     * @param mixed $result
     * @return bool
     */
    protected function afterFlight($result)
    {
        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }
}
