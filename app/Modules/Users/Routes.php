<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The default Auth Routes.
Route::get('login',  array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@login'
));

Route::post('login', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postLogin'
));

Route::get('logout', array(
    'before' => 'auth',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@logout'
));

// The Password Remind.
Route::get('password/remind', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@remind'
));

Route::post('password/remind', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postRemind'
));

// The Password Reset.
Route::get('password/reset/{token?}', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@reset'
));

Route::post('password/reset', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Authorize@postReset'
));

// The Account Registration.
Route::get('register', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@create'
));

Route::post('register', array(
    'before' => 'guest|csrf',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@store'
));

Route::get('register/verify/{token?}', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@verify'
));

Route::get('register/status', array(
    'before' => 'guest',
    'uses'   => 'App\Modules\Users\Controllers\Registrar@status'
));

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Users\Controllers\Admin'), function() {
    // The User's Profile.
    Route::get( 'users/profile', array('before' => 'auth',      'uses' => 'Users@profile'));
    Route::post('users/profile', array('before' => 'auth|csrf', 'uses' => 'Users@postProfile'));

    // The Users CRUD.
    Route::get( 'users',              array('before' => 'auth',      'uses' => 'Users@index'));
    Route::get( 'users/create',       array('before' => 'auth',      'uses' => 'Users@create'));
    Route::post('users',              array('before' => 'auth|csrf', 'uses' => 'Users@store'));
    Route::get( 'users/{id}',         array('before' => 'auth',      'uses' => 'Users@show'));
    Route::get( 'users/{id}/edit',    array('before' => 'auth',      'uses' => 'Users@edit'));
    Route::post('users/{id}',         array('before' => 'auth|csrf', 'uses' => 'Users@update'));
    Route::post('users/{id}/destroy', array('before' => 'auth|csrf', 'uses' => 'Users@destroy'));

    // The Users Search.
    Route::post( 'users/search', array('before' => 'auth', 'uses' => 'Users@search'));

    // The Roles CRUD.
    Route::get( 'roles',              array('before' => 'auth',      'uses' => 'Roles@index'));
    Route::get( 'roles/create',       array('before' => 'auth',      'uses' => 'Roles@create'));
    Route::post('roles',              array('before' => 'auth|csrf', 'uses' => 'Roles@store'));
    Route::get( 'roles/{id}',         array('before' => 'auth',      'uses' => 'Roles@show'));
    Route::get( 'roles/{id}/edit',    array('before' => 'auth',      'uses' => 'Roles@edit'));
    Route::post('roles/{id}',         array('before' => 'auth|csrf', 'uses' => 'Roles@update'));
    Route::post('roles/{id}/destroy', array('before' => 'auth|csrf', 'uses' => 'Roles@destroy'));
});
