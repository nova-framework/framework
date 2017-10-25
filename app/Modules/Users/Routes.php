<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

// The User's Account.
Route::get( 'account',         array('before' => 'auth', 'uses' => 'Account@index'));
Route::post('account',         array('before' => 'auth', 'uses' => 'Account@update'));
Route::post('account/picture', array('before' => 'auth', 'uses' => 'Account@picture'));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    // The Users Search.
    Route::post('users/search', array('before' => 'auth', 'uses' => 'Users@search'));

    // The Users CRUD.
    Route::get( 'users',              array('before' => 'auth', 'uses' => 'Users@index'));
    Route::get( 'users/create',       array('before' => 'auth', 'uses' => 'Users@create'));
    Route::post('users',              array('before' => 'auth', 'uses' => 'Users@store'));
    Route::get( 'users/{id}',         array('before' => 'auth', 'uses' => 'Users@show'));
    Route::get( 'users/{id}/edit',    array('before' => 'auth', 'uses' => 'Users@edit'));
    Route::post('users/{id}',         array('before' => 'auth', 'uses' => 'Users@update'));
    Route::post('users/{id}/destroy', array('before' => 'auth', 'uses' => 'Users@destroy'));
});
