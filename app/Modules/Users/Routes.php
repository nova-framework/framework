<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Auth Routes.
Router::get('login',  array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@login'
));

Router::post('login', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postLogin'
));

Router::get('logout', array(
    'before' => 'auth',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@logout'
));

// The Password Remind.
Router::get('password/remind', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@remind'
));

Router::post('password/remind', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postRemind'
));

// The Password Reset.
Router::get('password/reset(/(:any))', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@reset'
));

Router::post('password/reset', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postReset'
));

// The Account Registration.
Router::get('register', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@create'
));

Router::post('register', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@store'
));

Router::get('register/verify/(:any)', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@verify'
));

Router::get('register/status', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@status'
));

// The Adminstration Routes.
Router::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Users\Controllers\Admin'), function() {
    // The User's Profile.
    Router::get( 'users/profile', array('before' => 'auth',      'uses' => 'Users@profile'));
    Router::post('users/profile', array('before' => 'auth|csrf', 'uses' => 'Users@postProfile'));

    // The Users CRUD.
    Router::get( 'users',                array('before' => 'auth',      'uses' => 'Users@index'));
    Router::get( 'users/create',         array('before' => 'auth',      'uses' => 'Users@create'));
    Router::post('users',                array('before' => 'auth|csrf', 'uses' => 'Users@store'));
    Router::get( 'users/(:num)',         array('before' => 'auth',      'uses' => 'Users@show'));
    Router::get( 'users/(:num)/edit',    array('before' => 'auth',      'uses' => 'Users@edit'));
    Router::post('users/(:num)',         array('before' => 'auth|csrf', 'uses' => 'Users@update'));
    Router::post('users/(:num)/destroy', array('before' => 'auth|csrf', 'uses' => 'Users@destroy'));

    // The Users Search.
    Router::post( 'users/search', array('before' => 'auth', 'uses' => 'Users@search'));

    // The Roles CRUD.
    Router::get( 'roles',                array('before' => 'auth',      'uses' => 'Roles@index'));
    Router::get( 'roles/create',         array('before' => 'auth',      'uses' => 'Roles@create'));
    Router::post('roles',                array('before' => 'auth|csrf', 'uses' => 'Roles@store'));
    Router::get( 'roles/(:num)',         array('before' => 'auth',      'uses' => 'Roles@show'));
    Router::get( 'roles/(:num)/edit',    array('before' => 'auth',      'uses' => 'Roles@edit'));
    Router::post('roles/(:num)',         array('before' => 'auth|csrf', 'uses' => 'Roles@update'));
    Router::post('roles/(:num)/destroy', array('before' => 'auth|csrf', 'uses' => 'Roles@destroy'));
});
