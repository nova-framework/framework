<?php

/*
|--------------------------------------------------------------------------
| Module Routes
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
    Route::get( 'roles',              array('before' => 'auth', 'uses' => 'Roles@index'));
    Route::get( 'roles/create',       array('before' => 'auth', 'uses' => 'Roles@create'));
    Route::post('roles',              array('before' => 'auth', 'uses' => 'Roles@store'));
    Route::get( 'roles/{id}',         array('before' => 'auth', 'uses' => 'Roles@show'));
    Route::get( 'roles/{id}/edit',    array('before' => 'auth', 'uses' => 'Roles@edit'));
    Route::post('roles/{id}',         array('before' => 'auth', 'uses' => 'Roles@update'));
    Route::post('roles/{id}/destroy', array('before' => 'auth', 'uses' => 'Roles@destroy'));
});
