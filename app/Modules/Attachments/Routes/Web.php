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


Route::group(array('prefix' => '/', 'middleware' => 'auth'), function ()
{
    // The Attachments.
    Route::post('attachments',              array('uses' => 'Attachments@store'));
    Route::post('attachments/done',         array('uses' => 'Attachments@done'));
    Route::post('attachments/{id}/destroy', array('uses' => 'Attachments@destroy'));

    // The authentication protected File serving.
    Route::get('attachments/{method}/{token}/{filename}', array(
        'uses'  => 'Attachments@serve',
        'where' => array(
            'method' => '(download|preview)',
        ),
    ));
});

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function ()
{
    Route::get( 'attachments',  array('uses' => 'Attachments@index'));
    Route::post('attachments',  array('uses' => 'Attachments@update'));
});
