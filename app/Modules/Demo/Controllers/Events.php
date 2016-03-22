<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers;

use App\Modules\Demo\Core\BaseController;
use Nova\Core\View;
use Nova\Events\Manager as EventManager;

/**
 * Sample Themed Controller with its typical usage.
 */
class Events extends BaseController
{
    private $filePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        $method = $this->method();

        $viewsPath = str_replace(BASEPATH, '', $this->viewsPath());

        $this->filePath = $viewsPath.$method.'.php';

        // Leave to parent's method the Flight decisions.
        return parent::before();
    }

    /**
     * Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        $params = array('path' => $this->filePath);

        // Get the Message.
        $message = '';

        EventManager::sendEvent('welcome', $params, $message);

        // Setup the View variables.
        $this->title(__d('demo', 'Welcome'));

        $this->set('message', $message);
    }
}
