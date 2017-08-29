<?php
/**
 * Dashboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 */

namespace Backend\Controllers\Admin;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Language;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use Backend\Controllers\BaseController;
use Backend\Models\User;
use Backend\Notifications\Sample as SampleNotification;

use Carbon\Carbon;

// For testing.
use Nova\Support\Facades\Event;
use Backend\Support\Menu;


class Dashboard extends BaseController
{

    public function data()
    {
        $format = __d('backend', '%d %b %Y, %H:%M');

        $columns = array(
            array('data' => 'userid',   'field' => 'id'),
            array('data' => 'username', 'field' => 'username'),

            array('data' => 'role', 'field' => 'role_id', 'uses' => function($user)
            {
                return $user->role->name;
            }),

            array('data' => 'first_name', 'field' => 'first_name'),
            array('data' => 'last_name',  'field' => 'last_name'),
            array('data' => 'email',      'field' => 'email'),

            array('data' => 'date', 'uses' => function($user) use ($format)
            {
                $activity = $user->activities->first();

                return Carbon::createFromTimestamp($activity->last_activity)
                    ->formatLocalized($format);
            }),

            array('data' => 'actions', 'uses' => function($online)
            {
                return '-';
            }),
        );

        $input = Input::only('draw', 'columns', 'start', 'length', 'search', 'order');

        //
        $activityLimit = Config::get('backend::activityLimit');

        $since = Carbon::now()->subMinutes($activityLimit)->timestamp;

        $query = User::with('role')->activeSince($since);

        //
        $data = $this->dataTable($query, $input, $columns);

        return Response::json($data);
    }

    public function index()
    {
        $debug = '';

        //
        $langInfo = Language::info();

        return $this->createView(compact('langInfo', 'debug'))
            ->shares('title', __d('backend', 'Dashboard'));
    }

    public function notify()
    {
        $authUser = Auth::user();

        //
        $authUser->notify(new SampleNotification());

        return Redirect::to('admin/dashboard')->with('success', 'A sample notification was sent to yourself.');
    }
}
