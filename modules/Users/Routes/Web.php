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
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function ()
{
    // The Custom Fields CRUD.
    Route::get( 'users/fields',              'FieldItems@index');
    Route::get( 'users/fields/create',       'FieldItems@create');
    Route::post('users/fields',              'FieldItems@store');
    Route::get( 'users/fields/{id}',         'FieldItems@show');
    Route::get( 'users/fields/{id}/edit',    'FieldItems@edit');
    Route::post('users/fields/{id}',         'FieldItems@update');
    Route::post('users/fields/{id}/destroy', 'FieldItems@destroy');

    // Server Side Processor for Users DataTable.
    Route::post('users/data',         'Users@data');

    // The Users CRUD.
    Route::get( 'users',              'Users@index');
    Route::get( 'users/create',       'Users@create');
    Route::post('users',              'Users@store');
    Route::get( 'users/{id}',         'Users@show');
    Route::get( 'users/{id}/edit',    'Users@edit');
    Route::post('users/{id}',         'Users@update');
    Route::post('users/{id}/destroy', 'Users@destroy');
});
