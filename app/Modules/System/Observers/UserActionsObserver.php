<?php

namespace App\Modules\System\Observers;

use Nova\Support\Facades\Auth;

use App\Modules\System\Models\UserLogs;


class UserActionsObserver
{
    public function saved($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            UserLogs::create(array(
                'user_id'      => $user->getKey(),
                'action'       => 'saved',
                'action_model' => get_class($model),
                'action_id'    => $model->getKey()
            ));
        }
    }

    public function updated($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            UserLogs::create(array(
                'user_id'      => $user->getKey(),
                'action'       => 'updated',
                'action_model' => get_class($model),
                'action_id'    => $model->getKey()
            ));
        }
    }

    public function deleting($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            UserLogs::create(array(
                'user_id'      => $user->getKey(),
                'action'       => 'deleted',
                'action_model' => get_class($model),
                'action_id'    => $model->getKey()
            ));
        }
    }
}
