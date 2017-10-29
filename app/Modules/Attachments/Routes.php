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

Route::group(array('middleware' => 'auth'), function ()
{
    // The Attachments.
    Route::post('attachments',              array('middleware' => 'auth', 'uses' => 'Attachments@store'));
    Route::post('attachments/done',         array('middleware' => 'auth', 'uses' => 'Attachments@done'));
    Route::post('attachments/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Attachments@destroy'));

    // The authentication protected File serving.
    Route::get('attachments/{method}/{token}/{filename}', array(
        'middleware' => 'auth',

        'uses'  => 'Attachments@serve',
        'where' => array(
            'method' => '(download|preview)',
        ),
    ));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    Route::get( 'attachments',  array('middleware' => 'auth', 'uses' => 'Attachments@index'));
    Route::post('attachments',  array('middleware' => 'auth', 'uses' => 'Attachments@update'));
});
