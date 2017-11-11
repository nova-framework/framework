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


Route::get('content/{slug}', array(
    'uses'  => 'Posts@index',
    'where' => array(
        'slug' => '(.*)',
    ),
));


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function ()
{
    Route::get( 'content/sample',  array('middleware' => 'auth', 'uses' => 'Posts@sample'));

    // The Content CRUD.
    Route::get( 'content/create/{type}', array('middleware' => 'auth', 'uses' => 'Posts@create'));
    Route::post('content',               array('middleware' => 'auth', 'uses' => 'Posts@store'));
    Route::get( 'content/{id}',          array('middleware' => 'auth', 'uses' => 'Posts@show'))->where('id', '\d+');
    Route::get( 'content/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Posts@edit'));
    Route::post('content/{id}',          array('middleware' => 'auth', 'uses' => 'Posts@update'));
    Route::post('content/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Posts@destroy'));

    Route::get('content/categories',    array('middleware' => 'auth', 'uses' => 'Taxonomies@index'));
    Route::get('content/tags',          array('middleware' => 'auth', 'uses' => 'Taxonomies@tags'));

    Route::get('content/{type}/{slug}', array('middleware' => 'auth', 'uses' => 'Posts@taxonomy'));
    Route::get('content/{type?}',       array('middleware' => 'auth', 'uses' => 'Posts@index'));
});
