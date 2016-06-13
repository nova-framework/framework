<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Router;


/** Define static routes. */

// The Adminstration Routes.
Router::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Settings\Controllers\Admin'), function() {
    Router::get( 'settings', array('filters' => 'auth',      'uses' => 'Settings@index'));
    Router::post('settings', array('filters' => 'auth|csrf', 'uses' => 'Settings@store'));
});

