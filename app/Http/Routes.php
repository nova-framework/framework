<?php
/**
 * Routes - all standard Routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 4.0
 */


/** Define static routes. */

// Default Routing
$router->any('/',       'Welcome@index');
$router->any('subpage', 'Welcome@subPage');

/** End default Routes */
