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

Route::group(array('prefix' => 'admin', 'namespace' => 'App\Modules\Messenger\Controllers\Admin'), function () {
    Route::get( 'messages',        array('before' => 'auth',      'as' => 'messages',        'uses' => 'Messages@index'));
    Route::get( 'messages/create', array('before' => 'auth',      'as' => 'messages.create', 'uses' => 'Messages@create'));
    Route::post('messages',        array('before' => 'auth|csrf', 'as' => 'messages.store',  'uses' => 'Messages@store'));
    Route::get( 'messages/{id}',   array('before' => 'auth',      'as' => 'messages.show',   'uses' => 'Messages@show'));
    Route::post('messages/{id}',   array('before' => 'auth|csrf', 'as' => 'messages.update', 'uses' => 'Messages@update'));
});
