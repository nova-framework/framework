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
    // Server Side Processor for Roles DataTable.
    Route::post('roles/data', 'Roles@data');

    // The Users CRUD.
    Route::get( 'roles',              'Roles@index');
    Route::get( 'roles/create',       'Roles@create');
    Route::post('roles',              'Roles@store');
    Route::get( 'roles/{id}',         'Roles@show')->where('id', '\d+');
    Route::get( 'roles/{id}/edit',    'Roles@edit')->where('id', '\d+');
    Route::post('roles/{id}',         'Roles@update')->where('id', '\d+');
    Route::post('roles/{id}/destroy', 'Roles@destroy')->where('id', '\d+');
});
