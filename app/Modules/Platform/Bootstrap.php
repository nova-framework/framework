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
