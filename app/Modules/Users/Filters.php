<?php
/**
 * Routing Filters - all Module's specific Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Route Filters. */

// Role-based Authorization Filter.
Route::filter('roles', function($route, $request, $response, $roles = null) {
    if (! is_null($roles) && ! Auth::user()->hasRole($roles)) {
         $status = __('You are not authorized to access this resource.');

         return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
    }
});
