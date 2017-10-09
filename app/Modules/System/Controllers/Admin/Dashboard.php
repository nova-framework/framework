<?php

namespace App\Modules\System\Controllers\Admin;

use App\Modules\System\Controllers\Admin\BaseController;

use View;


class Dashboard extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('system', 'Dashboard'));
    }

}
