<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 19th, 2016
 */

use Core\Route;


/** Define Route Filters. */

// A Testing Filter which dump the matched Route.
Route::filter('test', function($route) {
    echo '<pre>' .var_export($route, true) .'</pre>';
});
