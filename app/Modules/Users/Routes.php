<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

Route::group(array('prefix' => '', 'namespace' => 'App\Modules\Users\Controllers'), function()
{
    // The default Auth Routes.
    Route::get( 'login',  array('before' => 'guest',      'uses' => 'Authorize@login'));
    Route::post('login',  array('before' => 'guest|csrf', 'uses' => 'Authorize@postLogin'));
    Route::get( 'logout', array('before' => 'auth',       'uses' => 'Authorize@logout'));

    // The Password Remind.
    Route::get( 'password/remind', array('before' => 'guest',      'uses' => 'Authorize@remind'));
    Route::post('password/remind', array('before' => 'guest|csrf', 'uses' => 'Authorize@postRemind'));

    // The Password Reset.
    Route::get( 'password/reset/{token}', array('before' => 'guest',      'uses' => 'Authorize@reset'));
    Route::post('password/reset',         array('before' => 'guest|csrf', 'uses' => 'Authorize@postReset'));

    // The Account Registration.
    Route::get( 'register',                 array('before' => 'guest',      'uses' => 'Registrar@create'));
    Route::post('register',                 array('before' => 'guest|csrf', 'uses' => 'Registrar@store'));
    Route::get( 'register/verify/{token?}', array('before' => 'guest',      'uses' => 'Registrar@verify'));
    Route::get( 'register/status',          array('before' => 'guest',      'uses' => 'Registrar@status'));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Users\Controllers\Admin'), function()
{
    // The User's Profile.
    Route::get( 'profile', array('before' => 'auth',      'uses' => 'Profile@index'));
    Route::post('profile', array('before' => 'auth|csrf', 'uses' => 'Profile@update'));

    // The Users Search.
    Route::post('users/search', array('before' => 'auth', 'uses' => 'Users@search'));

    // The Users CRUD.
    Route::get( 'users',              array('before' => 'auth',      'uses' => 'Users@index'));
    Route::get( 'users/create',       array('before' => 'auth',      'uses' => 'Users@create'));
    Route::post('users',              array('before' => 'auth|csrf', 'uses' => 'Users@store'));
    Route::get( 'users/{id}',         array('before' => 'auth',      'uses' => 'Users@show'));
    Route::get( 'users/{id}/edit',    array('before' => 'auth',      'uses' => 'Users@edit'));
    Route::post('users/{id}',         array('before' => 'auth|csrf', 'uses' => 'Users@update'));
    Route::post('users/{id}/destroy', array('before' => 'auth|csrf', 'uses' => 'Users@destroy'));
});
