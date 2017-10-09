<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;

use App\Modules\System\Controllers\BaseController;
use App\Modules\System\Notifications\Sample as SampleNotification;


class Dashboard extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('system', 'Dashboard'));
    }

    public function notify()
    {
        $user = Auth::user();

        //
        $user->notify(new SampleNotification());

        return Redirect::to('dashboard')->withStatus('A sample notification was sent to yourself.', 'success');
    }
}
