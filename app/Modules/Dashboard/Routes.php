<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstrations's Dashboard.
Router::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Dashboard\Controllers\Admin'), function() {
    Router::get('',          array('before' => 'auth', 'uses' => 'Dashboard@index'));
    Router::get('dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));
});
