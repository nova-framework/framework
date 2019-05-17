<?php

namespace Modules\Platform\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\View;

use App\Controllers\BaseController as Controller;

use Modules\Platform\Models\Activity;
use Modules\Platform\Support\EventedMenu;


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
    protected $layout = 'Frontend';


    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();

        // Update the User Activity.
        Activity::updateCurrent(
            $request = $this->getRequest()
        );

        // Update the Frontend Menus.
        if (! is_null($user = Auth::user())) {
            $url = $request->url();

            $navbarLeftItems  = EventedMenu::get('frontend.menu.left',  $user, $url);
            $navbarRightItems = EventedMenu::get('frontend.menu.right', $user, $url);
        } else {
            $navbarLeftItems  = array();
            $navbarRightItems = array();
        }

        View::share('navbarLeftItems',  $navbarLeftItems);
        View::share('navbarRightItems', $navbarRightItems);
    }
}
