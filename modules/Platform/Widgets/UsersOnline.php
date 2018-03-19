<?php

namespace Modules\Platform\Widgets;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use Modules\Users\Models\User;

use Carbon\Carbon;


class UsersOnline extends Widget
{

    public function render()
    {
        $activityLimit = Config::get('platform::activityLimit');

        $since = Carbon::now()->subMinutes($activityLimit)->timestamp;

        $users = User::with('roles')->activeSince($since)->paginate(25);

        return View::make('Modules/Platform::Widgets/DashboardUsersOnline', compact('users'))->render();
    }
}
