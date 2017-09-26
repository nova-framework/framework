<?php
/**
 * Routing Filters - all Module's specific Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Route Filters. */

// Role-based Authorization Filter.
Route::filter('role', function ($route, $request, $role)
{
    $roles = array_slice(func_get_args(), 2);

    // Get the default Auth Guard.
    $guard = Config::get('auth.defaults.guard', 'web');

    $user = Auth::guard($guard)->user();

    if (! is_null($user) && ! $user->hasRole($roles)) {
        if ($request->ajax() || $request->wantsJson()) {
            // On an AJAX Request; we return a response: Error 403 (Access denied)
            return Response::make('', 403);
        }

        // Get the Guard's paths from configuration.
        $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'admin/dashboard');

        $status = __d('system', 'You are not authorized to access this resource.');

        return Redirect::to($uri)->withStatus($status, 'warning');
    }
});
