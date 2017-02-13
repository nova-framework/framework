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
Route::middleware('admin', function($request, $next)
{
    $user = Auth::user();

    // Check the User Authorization - while using the Extended Auth Driver.
    if ($user->hasRole('administrator')) {
        return $next($request);
    }

    if ($request->ajax()) {
        // On an AJAX Request; just return Error 403 (Access denied)
        return Response::make('', 403);
    }

    $status = __('You are not authorized to access this resource.');

    return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
});

// Role-based Authorization Middleware.
Route::middleware('roles', function($request, $next, $value) {
    $user = Auth::user();

    // Explode the passed value on array of accepted User Roles.
    $roles = explode(';', $value);

    if (! $user->hasRole($roles)) {
        $status = __('You are not authorized to access this resource.');

        return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
    }

    return $next($request);
});

