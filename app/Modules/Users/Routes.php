<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Routing\Router;


/** Define static routes. */

// The default Auth Routes.
Router::get('login',  array(
    'filters' => 'guest|csrf',
    'uses' => 'App\Modules\Users\Controllers\Users@login'
));

Router::post('login', array(
    'filters' => 'guest|csrf',
    'uses' => 'App\Modules\Users\Controllers\Users@postLogin'
));

Router::get('logout', array(
    'filters' => 'auth',
    'uses' => 'App\Modules\Users\Controllers\Users@logout'
));

// The User's Dashboard.
Router::get('dashboard', array(
    'filters' => 'auth',
    'uses' => 'App\Modules\Users\Controllers\Users@dashboard'
));

// The User's Profile.
Router::get('profile', array(
    'filters' => 'auth|csrf',
    'uses' => 'App\Modules\Users\Controllers\Users@profile'
));

Router::post('profile', array(
    'filters' => 'auth|csrf',
    'uses' => 'App\Modules\Users\Controllers\Users@postProfile'
));

