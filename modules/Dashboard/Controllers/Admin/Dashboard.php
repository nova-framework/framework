<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Dashboard\Controllers\Admin;

use Nova\Support\Facades\View;
use Nova\Support\Facades\Widget;

use App\Core\BackendController;


class Dashboard extends BackendController
{

    public function index()
    {
        $smallBoxUsers = Widget::smallBoxUsers();

        return $this->getView()
            ->shares('title', __d('dashboard', 'Dashboard'))
            ->withSmallBoxUsers($smallBoxUsers);
    }

}
