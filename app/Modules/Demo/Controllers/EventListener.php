<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers;

use Nova\Core\Event;
use Nova\Core\View;
use App\Core\ClassicController;

/**
 * Sample Themed Controller with its typical usage.
 */
class EventListener extends ClassicController
{

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return the Welcome message.
     *
     * @param mixed $event
     * @return string|void
     */
    public function welcome($event)
    {
        $params = $event->params();

        $message = __d('demo', 'Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $params['path']);

        return $message;
    }
}
