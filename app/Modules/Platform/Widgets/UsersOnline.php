<?php

namespace App\Modules\Platform\Widgets;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Users\Models\User;

use Carbon\Carbon;


class UsersOnline extends Widget
{

    public function render(array $parameters = array())
    {
        $activityLimit = Config::get('platform::activityLimit');

        $since = Carbon::now()->subMinutes($activityLimit)->timestamp;

        $users = User::with('roles')->activeSince($since)->paginate(25);

        return View::make('Partials/DashboardUsersOnline', compact('users'), 'Platform')->render();
    }
}
