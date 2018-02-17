<?php

/*
|--------------------------------------------------------------------------
| Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Nova the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    // The Users Profile.
    Route::get( 'profile',              array('middleware' => 'auth', 'uses' => 'Profiles@index'));
    Route::post('profile',              array('middleware' => 'auth', 'uses' => 'Profiles@store'));
    Route::post('profile/{id}',         array('middleware' => 'auth', 'uses' => 'Profiles@update'));
    Route::post('profile/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Profiles@destroy'));

    // The Users Search.
    Route::get('users/search', array('middleware' => 'auth', 'uses' => 'Users@search'));

    // The Users CRUD.
    Route::get( 'users',              array('middleware' => 'auth', 'uses' => 'Users@index'));
    Route::get( 'users/create',       array('middleware' => 'auth', 'uses' => 'Users@create'));
    Route::post('users',              array('middleware' => 'auth', 'uses' => 'Users@store'));
    Route::get( 'users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@show'));
    Route::get( 'users/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Users@edit'));
    Route::post('users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@update'));
    Route::post('users/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Users@destroy'));
});
