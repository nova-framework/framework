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

// The Adminstration Routes.
Router::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Users\Controllers\Admin'), function() {
    // The User's Profile.
    Router::get( 'users/profile', array('filters' => 'auth',      'uses' => 'Users@profile'));
    Router::post('users/profile', array('filters' => 'auth|csrf', 'uses' => 'Users@postProfile'));

    // The Users CRUD.
    Router::get( 'users',                array('filters' => 'auth',      'uses' => 'Users@index'));
    Router::get( 'users/create',         array('filters' => 'auth',      'uses' => 'Users@create'));
    Router::post('users',                array('filters' => 'auth|csrf', 'uses' => 'Users@store'));
    Router::get( 'users/(:num)',         array('filters' => 'auth',      'uses' => 'Users@show'));
    Router::get( 'users/(:num)/edit',    array('filters' => 'auth',      'uses' => 'Users@edit'));
    Router::post('users/(:num)',         array('filters' => 'auth|csrf', 'uses' => 'Users@update'));
    Router::post('users/(:num)/destroy', array('filters' => 'auth|csrf', 'uses' => 'Users@destroy'));

    // The Users Search.
    Router::post( 'users/search', array('filters' => 'auth', 'uses' => 'Users@search'));
});
