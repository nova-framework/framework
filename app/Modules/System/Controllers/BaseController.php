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
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;
use Nova\Support\Arr;
use Nova\Support\Str;

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
        View::share('menuItems', $this->getMenuItems('backend.menu', $user));
    }

    /**
     * Get the menu items for the specified Event and User.
     *
     * @param  string  $event
     * @param  mixed  $user
     * @return array
     */
    protected function getMenuItems($event, $user)
    {
        if (is_null($user)) {
            return array(); // The User is not authenticated?
        }

        $url = Request::url();

        // Fire the specified Event.
        $results = Event::fire($event, array($user));

        //
        $path = '';

        $items = array();

        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            foreach ($result as $item) {
                if (! isset($item['children'])) {
                    $item['children'] = array();
                }

                $key = str_replace('.', '.children.', $item['path']);

                Arr::set($items, $key, $item);

                if ($item['url'] == $url) {
                    $path = $item['path'];
                }
            }
        }

        foreach ($items as &$item) {
            $children = Arr::get($item, 'children', array());

            $item['children'] = static::prepareItems($children, $url, $path);
        }

        return static::prepareItems($items, $url, $path);
    }

    /**
     * Prepare the given menu items.
     *
     * @param  array  $items
     * @param  string $url
     * @return array
     */
    protected static function prepareItems(array $items, $url, $path)
    {
        foreach ($items as &$item) {
            $active = false;

            if (($item['url'] == $url) || Str::startsWith($path, $item['path'])) {
                $active = true;
            }

            $item['active'] = $active;
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
