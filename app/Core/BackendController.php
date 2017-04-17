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
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\View;

use App\Core\ThemedController;

use Modules\Messages\Models\Message;
use Modules\System\Models\Notification;


abstract class BackendController extends ThemedController
{
    /**
     * The currently used Template.
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
    protected function before()
    {
        if (! Auth::check()) {
            // The User is not authenticated; nothing to do.
            return;
        }

        // The User is logged in; setup the Backend Menu.
        $user = Auth::user();

        //
        $items = $this->getMenuItems($user);

        View::share('menuItems', $items);

        //
        $notifications = Notification::where('user_id', $user->id)->unread()->count();

        View::share('notificationCount', $notifications);

        //
        $messages = Message::where('receiver_id', $user->id)->unread()->count();

        View::share('privateMessageCount', $messages);
    }

    private function getMenuItems($user)
    {
        $items = array();

        // Prepare the Event payload.
        $payload = array($user);

        // Fire the Event 'backend.menu' and store the results.
        $results = Event::fire('backend.menu', $payload);

        // Merge all results on a menu items array.
        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $items = array_merge($items, $result);
            }
        }

        // Sort the base menu items by their weight and title.
        $items = array_sort($items, function($value) {
            return sprintf('%06d - %s', $value['weight'], $value['title']);
        });

        // Sort the child menu items by their weight and title.
        foreach ($items as &$item) {
            $children = array_get($item, 'children', array());

            if (empty($children)) continue;

            $children = array_sort($children, function($value) {
                return sprintf('%06d - %s', $value['weight'], $value['title']);
            });

            $item['children'] = $children;
        }

        return $items;
    }
}
