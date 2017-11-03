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
            $uuid = $request->input('read');

            $notification = Notification::where('uuid', $uuid)->first();

            if (! is_null($notification)) {
                $guard = $this->getGuardByAuthModel($notification->notifiable_type);

                $user = Auth::guard($guard)->user();

                if (! is_null($user) && ($user->id == $notification->notifiable_id)) {
                    $notification->markAsRead();
                }
            }
        }

        return $next($request);
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
