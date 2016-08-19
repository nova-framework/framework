<?php
/**
 * Routes - all standard Routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Routing
Route::get('/',       'App\Controllers\Welcome@index');
Route::get('subpage', 'App\Controllers\Welcome@subPage');

//
// The catch-all Route - when enabled, it will capture any URI, with any HTTP Method.
// NOTE: ensure that it is the last one defined, otherwise it will mask other Routes.

//Route::any('(:all)', 'App\Controllers\Demo@catchAll');
Route::any('{slug}', 'App\Controllers\Demo@catchAll')->where('slug', '(.*)');

/** End default Routes */
