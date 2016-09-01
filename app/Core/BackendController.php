<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Http\Request;
use Routing\Route;

use App\Core\Controller as BaseController;

use Auth;
use Redirect;


abstract class BackendController extends BaseController
{
    /**
     * The currently used Template.
     *
     * @var string
     */
    protected $template = 'AdminLte';

    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'backend';

    /**
     * Create a new BackendController instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * A Before Filter which permit the access to Administrators.
     */
    public function adminUsersFilter(Route $route, Request $request)
    {
        // Check the User Authorization - while using the Extended Auth Driver.
        if (! Auth::user()->hasRole('administrator')) {
            $status = __d('users', 'You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }
    }
}
