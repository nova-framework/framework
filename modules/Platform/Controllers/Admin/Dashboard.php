<?php

namespace Modules\Platform\Controllers\Admin;

use Modules\Platform\Controllers\Admin\BaseController;


class Dashboard extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Dashboard'));
    }
}
