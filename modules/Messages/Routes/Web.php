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


// The Private Messages.
Route::group(array('middleware' => 'auth'), function ()
{
    Route::get( 'messages',            'Messages@index');
    Route::get( 'messages/create',     'Messages@create');
    Route::post('messages',            'Messages@store');
    Route::get( 'messages/{threadId}', 'Messages@show')->where('id', '\d+');

    //Route::post('messages/{postId}/destroy', 'Messages@destroy');

    Route::post('messages/{threadId}', 'Messages@reply')->where('id', '\d+');
});
