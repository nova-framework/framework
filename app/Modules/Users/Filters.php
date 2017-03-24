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
    if (! Auth::check()) {
        return Redirect::guest('login');
    }

    $roles = array_slice(func_get_args(), 2);

    if (! Auth::user()->hasRole($roles)) {
        $status = __d('users', 'You are not authorized to access this resource.');

        return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
    }
});
