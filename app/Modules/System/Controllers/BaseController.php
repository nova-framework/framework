<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\View;

use App\Controllers\BaseController as Controller;


abstract class BaseController extends Controller
{
    /**
     * The currently used Theme.
     *
     * @var string
     */
    protected $theme = 'AdminLite';

    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Backend';


    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();

        // Get the current User.
        $user = Auth::user();

        // Setup the main Menu.
        View::share('menuItems', $this->getMenuItems($user, 'backend.menu'));
    }

    /**
     * Get the menu items for the specified Event.
     *
     * @param  mixed  $user
     * @param  string  $event
     * @return array
     */
    protected function getMenuItems($user, $event)
    {
        if (is_null($user)) {
            return array();  // The User is not authenticated.
        }

        // Fire the specified Event.
        $results = Event::fire($event, array($user));

        // Prepare the menu items from the Event results.
        $items = array();

        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $items = array_merge($items, $result);
            }
        }

        // Sort the menu items by their weight and title.
        usort($items, function ($a, $b)
        {
            if ($a['weight'] === $b['weight']) {
                return strcmp($a['title'], $b['title']);
            }

            return ($a['weight'] < $b['weight']) ? -1 : 1;
        });

        return $items;
    }
}
