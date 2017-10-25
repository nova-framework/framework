<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Http\Request;


/**
 * Role-based Authorization Middleware.
 */
Route::middleware('role', function (Request $request, Closure $next, $role)
{
    $roles = array_slice(func_get_args(), 2);

    //
    $guard = Config::get('auth.default', 'web');

    $user = Auth::guard($guard)->user();

    if (! is_null($user) && ! $user->hasRole($roles)) {
        if ($request->ajax() || $request->wantsJson()) {
            // On an AJAX Request; we return a response: Error 403 (Access denied)
            return Response::make('', 403);
        }

        $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'dashboard');

        return Redirect::to($uri)
            ->withStatus(__d('platform', 'You are not authorized to access this resource.'), 'warning');
    }

    return $next($request);
});
