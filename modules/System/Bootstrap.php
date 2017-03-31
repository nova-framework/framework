<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

use Modules\System\Exceptions\ValidationException;


App::error(function(ValidationException $exception, $code)
{
    $errors = $exception->getErrors();

    return Redirect::back()->withInput()->withErrors($errors);
});

/** Define Route Middleware. */

/**
 * Permit the access only to Administrators.
 */
Route::middleware('admin', function($request, Closure $next, $guard = null)
{
    $guard = $guard ?: Config::get('auth.defaults.guard', 'web');

    $user = Auth::guard($guard)->user();

    // Check the User Authorization - while using the Extended Auth Driver.
    if (! is_null($user) && ! $user->hasRole('administrator')) {
        if ($request->ajax() || $request->wantsJson()) {
            // On an AJAX Request; just return Error 403 (Access denied)
            return Response::make('Forbidden', 403);
        }

        // Get the Guard's paths from configuration.
        $paths = Config::get("auth.guards.{$guard}.paths", array(
            'dashboard' => 'admin/dashboard'
        ));

        $status = __('You are not authorized to access this resource.');

        return Redirect::to($paths['dashboard'])->withStatus($status, 'warning');
    }

    return $next($request);
});

/**
 * Role-based Authorization Middleware.
 */
Route::middleware('role', function($request, Closure $next, $role)
{
    $roles = array_slice(func_get_args(), 2);

    //
    $guard = Config::get('auth.defaults.guard', 'web');

    $user = Auth::guard($guard)->user();

    if (! is_null($user) && ! $user->hasRole($roles)) {
        // Get the Guard's paths from configuration.
        $paths = Config::get("auth.guards.{$guard}.paths", array(
            'dashboard' => 'admin/dashboard'
        ));

        $status = __d('system', 'You are not authorized to access this resource.');

        return Redirect::to($paths['dashboard'])->withStatus($status, 'warning');
    }

    return $next($request);
});
