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
    Route::get('roles',             array('middleware' => 'paginate', 'uses' => 'Roles@index'));
    Route::get('roles/page/{page}', array('middleware' => 'paginate', 'uses' => 'Roles@index'))->where('page', '[0-9]+');

    Route::get( 'roles/create',       'Roles@create');
    Route::post('roles',              'Roles@store');
    Route::get( 'roles/{id}',         'Roles@show');
    Route::get( 'roles/{id}/edit',    'Roles@edit');
    Route::post('roles/{id}',         'Roles@update');
    Route::post('roles/{id}/destroy', 'Roles@destroy');
});
