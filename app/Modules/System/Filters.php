<?php
/**
 * Routing Filters - all Module's specific Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Route Filters. */


// Referer checking Filter.
Route::filter('referer', function($route, $request)
{
    // Check if the visitor come to this Route from another site.
    $referer = $request->header('referer');

    if (! starts_with($referer, Config::get('app.url'))) {
        return Redirect::back();
    }
});

