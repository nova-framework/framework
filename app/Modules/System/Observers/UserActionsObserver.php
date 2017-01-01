<?php

namespace App\Modules\System\Observers;

use Nova\Support\Facades\Auth;

use App\Modules\System\Models\UserLog;


class UserActionsObserver
{
    public function saved($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($model->wasRecentlyCreated == true) {
                // Data was just created
                $action = 'created';
            } else {
                // Data was updated
                $action = 'updated';
            }

            UserLog::create(array(
                'user_id'      => $user->getKey(),
                'action'       => $action,
                'action_model' => get_class($model),
                'action_id'    => $model->getKey()
            ));
        }
    }

    public function deleting($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            UserLog::create(array(
                'user_id'      => $user->getKey(),
                'action'       => 'deleted',
                'action_model' => get_class($model),
                'action_id'    => $model->getKey()
            ));
        }
    }
}
