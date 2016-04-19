<?php
/**
 * Routing Filters - all standard Routing Filters are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 19th, 2016
 */

/** Create alias for Router. */
use Nova\Net\Router;

/** Define static Filters. */

// A test Filter.
Router::filter('test', function($route) {
    echo '<pre>' .var_export($route, true) .'</pre>';

    return true;
});
