<?php

namespace App\Modules\Platform\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Modules\Platform\Controllers\BaseController;


class Notifications extends BaseController
{

    public function index()
    {
        $authUser = Auth::user();

        $notifications = $authUser->notifications()->paginate(25);

        return $this->createView()
            ->shares('title', __d('system', 'Notifications'))
            ->with('notifications', $notifications);
    }

    public function update()
    {
        $authUser = Auth::user();

        //
        $input = Input::only('nid');

        $validator = Validator::make($input,
            array('nid' => 'required|array|exists:notifications,id'), array(), array('id' => 'ID')
        );

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        $notifications = $authUser->notifications()->whereIn('id', $input['nid'])->get();

        $notifications->markAsRead();

        // Prepare the flash message.
        $status = __d('system', 'The selected notification(s) was successfully marked as read.');

        return Redirect::to('notifications')->withStatus($status);
    }
}
