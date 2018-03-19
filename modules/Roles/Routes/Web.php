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
    Route::get( 'roles',              array('middleware' => 'auth', 'uses' => 'Roles@index'));
    Route::get( 'roles/create',       array('middleware' => 'auth', 'uses' => 'Roles@create'));
    Route::post('roles',              array('middleware' => 'auth', 'uses' => 'Roles@store'));
    Route::get( 'roles/{id}',         array('middleware' => 'auth', 'uses' => 'Roles@show'));
    Route::get( 'roles/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Roles@edit'));
    Route::post('roles/{id}',         array('middleware' => 'auth', 'uses' => 'Roles@update'));
    Route::post('roles/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Roles@destroy'));
});
