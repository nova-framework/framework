<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Http\Request;
use Nova\Routing\Route;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\View;

use App\Core\Controller as BaseController;


abstract class BackendController extends BaseController
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
     * A Before Filter which permit the access to Administrators.
     */
    public function adminUsersFilter(Route $route, Request $request, $guard = null)
    {
        $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

        // Check the User Authorization.
        $user = Auth::guard($guard)->user();

        if (! is_null($user) && ! $user->hasRole('administrator')) {
            $status = __('You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }
    }

    /**
     * Method executed before any action.
     */
    protected function before()
    {
        // Setup the main Menu.
        View::share('menuItems', $this->getMenuItems());
    }

    protected function getMenuItems()
    {
        $user = Auth::user();

        if (is_null($user)) {
            // The User is not authenticated.
            return array();
        }

        // Prepare the Event payload.
        $payload = array($user);

        // Fire the Event 'backend.menu' and store the results.
        $results = Event::fire('backend.menu', $payload);

        // Merge all results on a menu items array.
        $items = array();

        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $items = array_merge($items, $result);
            }
        }

        // Sort the menu items by their weight and title.
        $items = array_sort($items, function($value)
        {
            return $value['weight'] .' - ' .$value['title'];
        });

        return $items;
    }
}
