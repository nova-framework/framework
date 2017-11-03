<?php

namespace App\Modules\Platform\Middleware;

use Nova\Http\Request;

use Closure;


class MarkNotificationAsRead
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->has('read')) {
            $uuid = $request->input('read');

            $notification = $request->user()->notifications()->where('uuid', $uuid)->first();

            if (! is_null($notification)) {
                $notification->markAsRead();
            }
        }

        return $next($request);
    }
}
