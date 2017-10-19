<?php

namespace App\Modules\Platform\Controllers\Admin;

use Nova\Support\Facades\Config;

use App\Modules\Platform\Controllers\Admin\BaseController;
use App\Modules\Users\Models\User;

use Carbon\Carbon;


class Dashboard extends BaseController
{

    public function index()
    {
        $activityLimit = Config::get('platform::activityLimit');

        $since = Carbon::now()->subMinutes($activityLimit)->timestamp;

        $users = User::with('roles')->activeSince($since)->paginate(25);

        return $this->createView()
            ->shares('title', __d('system', 'Dashboard'))
            ->with('users', $users);
    }

}
