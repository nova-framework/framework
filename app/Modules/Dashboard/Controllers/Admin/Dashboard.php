<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Dashboard\Controllers\Admin;

use Nova\Support\Facades\View;

use App\Core\BackendController;


class Dashboard extends BackendController
{

    public function index()
    {
        return $this->getView()
            ->shares('title', __d('dashboard', 'Dashboard'));
    }

}
