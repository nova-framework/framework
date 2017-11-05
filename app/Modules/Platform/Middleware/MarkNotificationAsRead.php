<?php

namespace App\Modules\Platform\Middleware;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;

use Shared\Notifications\DatabaseNotification as Notification;

use Closure;


class MarkNotificationAsRead
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->has('read')) {
            $notification = Notification::where('uuid', $request->input('read'))->first();

            if (! is_null($notification)) {
                $this->handleNotification($notification);
            }
        }

        return $next($request);
    }

    protected function handleNotification(Notification $notification)
    {
        $guard = $this->getGuardByAuthModel($notification->notifiable_type);

        if (! is_null($user = Auth::guard($guard)->user()) && ($user->id == $notification->notifiable_id)) {
            $notification->markAsRead();
        }
    }

    protected function getGuardByAuthModel($model)
    {
        if (is_null($provider = $this->getAuthProviderByModel($model))) {
            return;
        }

        $guards = Config::get('auth.guards', array());

        foreach ($guards as $guard => $options) {
            if ($options['provider'] == $provider) {
                return $guard;
            }
        }
    }

    protected function getAuthProviderByModel($model)
    {
        $providers = Config::get('auth.providers', array());

        foreach ($providers as $provider => $options) {
            if ($options['model'] == $model) {
                return $provider;
            }
        }
    }
}
