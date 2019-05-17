<?php

namespace Modules\Platform\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
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
    protected $layout = 'Backend';


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

        // Authorize the current User.
        if (Gate::denies('platform.backend.manage')) {
            throw new AuthorizationException();
        }

        if (! is_null($user = Auth::user())) {
            $url = $request->url();

            $navbarItems  = EventedMenu::get('backend.menu.navbar',  $user, $url);
            $sidebarItems = EventedMenu::get('backend.menu.sidebar', $user, $url);
        } else {
            $navbarItems =  array();
            $sidebarItems = array();
        }

        View::share('navbarItems',  $navbarItems);
        View::share('sidebarItems', $sidebarItems);
    }
}
