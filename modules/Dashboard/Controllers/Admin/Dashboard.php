<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Dashboard\Controllers\Admin;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\View;
use Nova\Support\Facades\Widget;

use App\Core\BackendController;


class Dashboard extends BackendController
{

    public function index()
    {
        $smallBoxUsers          = Widget::smallBoxUsers();
        $smallBoxUniqueVisitors = Widget::smallBoxUniqueVisitors();
        $smallBoxOrders         = Widget::smallBoxOrders();
        $smallBoxBounceRate     = Widget::smallBoxBounceRate();

        $debug = '';

        return $this->getView()
            ->shares('title', __d('backend', 'Dashboard'))
            ->with('smallBoxUsers', $smallBoxUsers)
            ->with('smallBoxUniqueVisitors', $smallBoxUniqueVisitors)
            ->with('smallBoxOrders', $smallBoxOrders)
            ->with('smallBoxBounceRate',$smallBoxBounceRate)
            ->with('debug', $debug);
    }

}
