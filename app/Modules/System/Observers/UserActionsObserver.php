<?php

namespace App\Modules\System\Observers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Request;

use App\Modules\System\Models\Log as Logger;


class UserActionsObserver
{
    public function saved($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($model->wasRecentlyCreated == true) {
                // Data was just created
                $action = __d('system', 'Created');
            } else {
                // Data was updated
                $action = __d('system', 'Updated');
            }

            Logger::create(array(
                'user_id'  => $user->getKey(),
                'group_id' => 2,
                'message'  => $this->message($model, $action),
            ));
        }
    }

    public function deleting($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $action = __d('system', 'Deleted');

            Logger::create(array(
                'user_id'  => $user->getKey(),
                'group_id' => 2,
                'message'  => $this->message($model, $action),
            ));
        }
    }

    protected function message($model, $action)
    {
        return __d('system', '{0} <b>{1}</b> with <b>ID: {2}</b>', $action, get_class($model), $model->getKey());
    }
}
