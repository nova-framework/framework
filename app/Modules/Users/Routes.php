<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function()
{
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

    // The Roles CRUD.
    Route::get( 'roles',              array('before' => 'auth',      'uses' => 'Roles@index'));
    Route::get( 'roles/create',       array('before' => 'auth',      'uses' => 'Roles@create'));
    Route::post('roles',              array('before' => 'auth|csrf', 'uses' => 'Roles@store'));
    Route::get( 'roles/{id}',         array('before' => 'auth',      'uses' => 'Roles@show'));
    Route::get( 'roles/{id}/edit',    array('before' => 'auth',      'uses' => 'Roles@edit'));
    Route::post('roles/{id}',         array('before' => 'auth|csrf', 'uses' => 'Roles@update'));
    Route::post('roles/{id}/destroy', array('before' => 'auth|csrf', 'uses' => 'Roles@destroy'));
});
