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


Route::post('contacts',  array('uses' => 'Contacts@store'));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    Route::get( 'contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@index'));
    Route::post('contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@store'));
    Route::post('contacts/{id}',          array('middleware' => 'auth', 'uses' => 'Contacts@update'))->where('id', '\d+');
    Route::post('contacts/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Contacts@destroy'))->where('id', '\d+');

    // The Contact Messages.
    Route::get( 'contacts/{id}', array('middleware' => 'auth', 'uses' => 'Messages@index'))->where('id', '\d+');

    Route::post('contacts/{contactId}/messages/{id}', array('middleware' => 'auth', 'uses' => 'Messages@destroy'))
        ->where('contactId', '\d+')
        ->where('id', '\d+');
});
