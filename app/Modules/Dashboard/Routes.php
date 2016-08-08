<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstrations's Dashboard.
Router::group(array('prefix' => '', 'namespace' => 'App\Modules\Dashboard\Controllers\Admin'), function() {
    Router::get('admin',           array('before' => 'auth', 'uses' => 'Dashboard@index'));
    Router::get('admin/dashboard', array('before' => 'auth', 'uses' => 'Dashboard@index'));
});
