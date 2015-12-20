<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Controllers\Demos;

use Nova\Core\View;
use Nova\Events\Manager as EventManager;
use App\Core\ClassicController;

/**
 * Sample Themed Controller with its typical usage.
 */
class Events extends ClassicController
{
    protected $layout = 'legacy';

    private $filePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeFlight()
    {
        $method = $this->method();

        $viewsPath = str_replace(BASEPATH, '', $this->viewsPath());

        $this->filePath = $viewsPath.$method.'.php';

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        $params = array('path' => $this->filePath);

        $message = $this->fetchMessage('welcome', $params);

        // Setup the View variables.
        $this->title(__('Welcome'));

        $this->set('message', $message);
    }

    private function fetchMessage($event, $params)
    {
        $eventManager = EventManager::getInstance();

        // Trigger the Event and capture the result.
        $message = '';

        $eventManager->trigger($event, $params, function($result) use (&$message) {
            $message .= $result;
        });

        return $message;
    }

}
