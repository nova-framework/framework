<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Auth\Access\AuthorizationException;
use Nova\Http\Request;


/**
 * Setup the Module Middleware.
 */
Route::pushMiddlewareToGroup('web', 'App\Modules\Platform\Middleware\MarkNotificationAsRead');


/**
 * Role-based Authorization Middleware.
 */
Route::middleware('role', function (Request $request, Closure $next, $role)
{
    $roles = array_slice(func_get_args(), 2);

    if (! is_null($user = Auth::user()) && ! $user->hasRole($roles)) {
        throw new AuthorizationException();
    }

    return $next($request);
});


/**
 * Register the Widgets.
 */
Widget::register('App\Modules\Platform\Widgets\UsersOnline', 'onlineUsers', 'backend.dashboard.content', 2);

Widget::register('App\Modules\Platform\Widgets\UsersOnline', 'onlineUsers', 'frontend.dashboard.content', 2);
