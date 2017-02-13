<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

Route::group(array('prefix' => '', 'namespace' => 'Modules\Users\Http\Controllers'), function()
{
    // The default Auth Routes.
    Route::get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
    Route::post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
    Route::get( 'logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

    // The Password Remind.
    Route::get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@remind'));
    Route::post('password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@postRemind'));

    // The Password Reset.
    Route::get( 'password/reset/{token}', array('middleware' => 'guest', 'uses' => 'Authorize@reset'));
    Route::post('password/reset',         array('middleware' => 'guest', 'uses' => 'Authorize@postReset'));

    // The Account Registration.
    Route::get( 'register',                 array('middleware' => 'guest', 'uses' => 'Registrar@create'));
    Route::post('register',                 array('middleware' => 'guest', 'uses' => 'Registrar@store'));
    Route::get( 'register/verify/{token?}', array('middleware' => 'guest', 'uses' => 'Registrar@verify'));
    Route::get( 'register/status',          array('middleware' => 'guest', 'uses' => 'Registrar@status'));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Modules\Users\Http\Controllers\Admin'), function()
{
    // The User's Profile.
    Route::get( 'profile', array('middleware' => 'auth',      'uses' => 'Profile@index'));
    Route::post('profile', array('middleware' => 'auth', 'uses' => 'Profile@update'));

    // The Users Search.
    Route::post('users/search', array('middleware' => 'auth', 'uses' => 'Users@search'));

    // The Users CRUD.
    Route::get( 'users',              array('middleware' => 'auth', 'uses' => 'Users@index'));
    Route::get( 'users/create',       array('middleware' => 'auth', 'uses' => 'Users@create'));
    Route::post('users',              array('middleware' => 'auth', 'uses' => 'Users@store'));
    Route::get( 'users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@show'));
    Route::get( 'users/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Users@edit'));
    Route::post('users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@update'));
    Route::post('users/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Users@destroy'));
});
