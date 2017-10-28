<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
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
