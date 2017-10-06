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

        // Get the items of the Backend Menu.
        if (! is_null($user = Auth::user())) {
            $menuItems = $this->getMenuItems('backend.menu', $user);
        } else {
            $menuItems = array();
        }

        View::share('menuItems', $menuItems);
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
        $url = Request::url();

        // Fire the specified Event and retrieve the responses.
        $results = Event::fire($event, array($user));

        // Build the menu items and find the current item path.
        list ($items, $path) = $this->buildMenuData($results, $url);

        foreach ($items as &$item) {
            $children = Arr::get($item, 'children', array());

            $item['children'] = $this->prepareItems($children, $path, $url);
        }

        return $this->prepareItems($items, $path, $url);
    }

    /**
     * Build the menu items array from results.
     *
     * @param  array  $results
     * @param  string $url
     * @return array
     */
    protected function buildMenuData(array $results, $url)
    {
        $path = '';

        $items = array();

        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            foreach ($result as $item) {
                $key = str_replace('.', '.children.', $item['path']);

                if (Arr::has($items, $key)) {
                    continue;
                }

                // Ensure the children array existence.
                else if (! isset($item['children'])) {
                    $item['children'] = array();
                }

                Arr::set($items, $key, $item);

                if (($item['url'] == $url) && empty($path)) {
                    $path = $item['path'];
                }
            }
        }

        return array($items, $path);
    }

    /**
     * Prepare the given menu items.
     *
     * @param  array  $items
     * @param  string $path
     * @param  string $url
     * @return array
     */
    protected function prepareItems(array $items, $path, $url)
    {
        // Setup the 'active' flag of the menu items.
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
