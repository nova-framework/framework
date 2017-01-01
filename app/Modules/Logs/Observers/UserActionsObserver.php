<?php

namespace App\Modules\Logs\Observers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Request;

use App\Modules\Logs\Models\Log;


class UserActionsObserver
{
    public function saved($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($model->wasRecentlyCreated == true) {
                // Data was just created
                $action = __d('system', 'created');
            } else {
                // Data was updated
                $action = __d('system', 'updated');
            }

            Log::create(array(
                'user_id'  => $user->getKey(),
                'group_id' => 2,
                'message'  => $this->getMessage($model, $action),
                'url'      => $this->getUrl(),
            ));
        }
    }

    public function deleting($model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $action = __d('system', 'deleted');

            Log::create(array(
                'user_id'  => $user->getKey(),
                'group_id' => 2,
                'message'  => $this->getMessage($model, $action),
                'url'      => $this->getUrl(),
            ));
        }
    }

    protected function getMessage($model, $action)
    {
        return __d('system', 'Was {0} <b>{1}</b> with <b>ID: {2}</b>', $action, get_class($model), $model->getKey());
    }

    protected function getUrl()
    {
        return Request::fullUrl();
    }

}
