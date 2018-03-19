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
    // The Custom Fields CRUD.
    Route::get( 'users/fields',              array('middleware' => 'auth', 'uses' => 'FieldItems@index'));
    Route::get( 'users/fields/create',       array('middleware' => 'auth', 'uses' => 'FieldItems@create'));
    Route::post('users/fields',              array('middleware' => 'auth', 'uses' => 'FieldItems@store'));
    Route::get( 'users/fields/{id}',         array('middleware' => 'auth', 'uses' => 'FieldItems@show'));
    Route::get( 'users/fields/{id}/edit',    array('middleware' => 'auth', 'uses' => 'FieldItems@edit'));
    Route::post('users/fields/{id}',         array('middleware' => 'auth', 'uses' => 'FieldItems@update'));
    Route::post('users/fields/{id}/destroy', array('middleware' => 'auth', 'uses' => 'FieldItems@destroy'));

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
