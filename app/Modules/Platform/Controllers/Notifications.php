<?php

namespace App\Modules\Platform\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;

use App\Modules\Platform\Controllers\BaseController;


class Notifications extends BaseController
{

    public function index()
    {
        $authUser = Auth::user();

        $notifications = $authUser->notifications()->paginate(25);

        return $this->createView()
            ->shares('title', __d('platform', 'Notifications'))
            ->with('notifications', $notifications);
    }

    public function update(Request $request)
    {
        $returnJson = $request->ajax() || $request->wantsJson();

        //
        $authUser = Auth::user();

        // Validate the input.
        $input = $request->only('nid');

        $validator = Validator::make($input,
            array('nid' => 'required|array|exists:notifications,id'), array(), array('id' => 'ID')
        );

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($returnJson) {
                return Response::json(array('errors' => $errors), 400);
            }

            return Redirect::back()->withInput()->withStatus($errors, 'danger');
        }

        $notifications = $authUser->notifications()->whereIn('id', $input['nid'])->get();

        $notifications->markAsRead();

        if ($returnJson) {
            return Response::json(array('success' => true), 200);
        }

        return Redirect::to('notifications')
            ->withStatus(__d('platform', 'The selected notification(s) was successfully marked as read.'), 'success');
    }
}
