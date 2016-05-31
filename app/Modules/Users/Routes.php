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
    'filters' => 'guest',
    'uses' => 'App\Modules\Users\Controllers\Authorize@login'
));

Router::post('login', array(
    'filters' => 'guest|csrf',
    'uses' => 'App\Modules\Users\Controllers\Authorize@postLogin'
));

Router::get('logout', array(
    'filters' => 'auth',
    'uses' => 'App\Modules\Users\Controllers\Authorize@logout'
));

// The Password Remind.
Router::get('password/remind', array(
    'filters' => 'guest',
    'uses' => 'App\Modules\Users\Controllers\Authorize@remind'
));

Router::post('password/remind', array(
    'filters' => 'guest|csrf',
    'uses' => 'App\Modules\Users\Controllers\Authorize@postRemind'
));

// The Password Reset.
Router::get('password/reset(/(:any))', array(
    'filters' => 'guest',
    'uses' => 'App\Modules\Users\Controllers\Authorize@reset'
));

Router::post('password/reset', array(
    'filters' => 'guest|csrf',
    'uses' => 'App\Modules\Users\Controllers\Authorize@postReset'
));

// The User's Dashboard.
Router::get('users/dashboard', array(
    'filters' => 'auth',
    'uses' => 'App\Modules\Users\Controllers\Users@dashboard'
));

// The User's Profile.
Router::get('users/profile', array(
    'filters' => 'auth',
    'uses' => 'App\Modules\Users\Controllers\Users@profile'
));

Router::post('users/profile', array(
    'filters' => 'auth|csrf',
    'uses' => 'App\Modules\Users\Controllers\Users@postProfile'
));
