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
    Route::get( 'permissions',       array('middleware' => 'auth', 'uses' => 'Permissions@index'));
    Route::post('permissions',       array('middleware' => 'auth', 'uses' => 'Permissions@update'));
});
