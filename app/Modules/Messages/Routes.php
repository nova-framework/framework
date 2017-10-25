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


// The Private Messages.
Route::get( 'messages',            array('before' => 'auth', 'uses' => 'Messages@index'));
Route::get( 'messages/create',     array('before' => 'auth', 'uses' => 'Messages@create'));
Route::post('messages',            array('before' => 'auth', 'uses' => 'Messages@store'));
Route::get( 'messages/{threadId}', array('before' => 'auth', 'uses' => 'Messages@show'));

//Route::post('messages/{postId}/destroy', array('before' => 'auth', 'uses' => 'Messages@destroy'));

Route::post('messages/{threadId}', array('before' => 'auth', 'uses' => 'Messages@reply'));
