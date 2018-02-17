<?php

namespace Modules\Platform\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Modules\Platform\Controllers\BaseController;
use Modules\Platform\Notifications\Sample as SampleNotification;


class Dashboard extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Dashboard'));
    }

    public function notify()
    {
        $user = Auth::user();

        //
        $user->notify(new SampleNotification());

        return Redirect::to('dashboard')->withStatus('A sample notification was sent to yourself.', 'success');
    }
}

