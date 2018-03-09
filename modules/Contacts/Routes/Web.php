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


Route::post('contacts',  array('uses' => 'Messages@store'));

// The authentication protected File serving.
Route::get('contacts/{method}/{token}/{filename}', array(
    'middleware' => 'auth',
    'uses'  => 'Attachments@serve',
    'where' => array(
        'method' => '(download|preview)',
    ),
));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    Route::get( 'contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@index'));
    Route::get( 'contacts/create',        array('middleware' => 'auth', 'uses' => 'Contacts@create'));
    Route::post('contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@store'));
    Route::get( 'contacts/{id}',          array('middleware' => 'auth', 'uses' => 'Contacts@show'))->where('id', '\d+');
    Route::get( 'contacts/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Contacts@edit'))->where('id', '\d+');
    Route::post('contacts/{id}',          array('middleware' => 'auth', 'uses' => 'Contacts@update'))->where('id', '\d+');
    Route::post('contacts/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Contacts@destroy'))->where('id', '\d+');

    // The Contact Messages.
    Route::get('contacts/{id}/messages', array('middleware' => 'auth', 'uses' => 'Messages@index'))->where('id', '\d+');

    Route::get('contacts/{id}/messages/search', array('middleware' => 'auth', 'uses' => 'Messages@search'))
        ->where('id', '\d+');

    Route::get('contacts/{cid}/messages/{mid}', array('middleware' => 'auth', 'uses' => 'Messages@show'))
        ->where('cid', '\d+')
        ->where('mid', '\d+');

    Route::post('contacts/{contactId}/messages/{id}', array('middleware' => 'auth', 'uses' => 'Messages@destroy'))
        ->where('contactId', '\d+')
        ->where('id', '\d+');

    Route::get( 'contacts/sample', array('middleware' => 'auth', 'uses' => 'Contacts@sample'));
});
