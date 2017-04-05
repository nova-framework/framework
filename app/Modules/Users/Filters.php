<?php
/**
 * Routing Filters - all Module's specific Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Route Filters. */


// Role-based Authorization Filter.
Route::filter('role', function($route, $request, $role)
{
    $roles = array_slice(func_get_args(), 2);

    // Get the default Auth Guard.
    $guard = Config::get('auth.defaults.guard', 'web');

    $user = Auth::guard($guard)->user();

    if (! is_null($user) && ! $user->hasRole($roles)) {
        // Get the Guard's paths from configuration.
        $paths = Config::get("auth.guards.{$guard}.paths", array(
            'dashboard' => 'admin/dashboard'
        ));

        $status = __d('users', 'You are not authorized to access this resource.');

        return Redirect::to($paths['dashboard'])->withStatus($status, 'warning');
    }
});
