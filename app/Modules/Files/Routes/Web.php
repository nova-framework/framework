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
    Route::get('files', array('middleware' => 'auth', 'uses' => 'Files@index'));

    Route::match(array('GET', 'POST'), 'files/connector', array('middleware' => 'auth', 'uses' => 'Files@connector'));

    // Thumbnails Files serving.
    Route::get('files/thumbnails/{file}', array('middleware' => 'auth', 'uses' => 'Files@thumbnails'));

    // Preview Files serving.
    Route::get('files/preview/{path}', array('middleware' => 'auth', 'uses' => 'Files@preview'))->where('path', '(.*)');
});
