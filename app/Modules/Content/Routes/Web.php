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
    // The Menus CRUD.
    Route::get( 'menus',               array('middleware' => 'auth', 'uses' => 'Menus@index'));
    Route::get( 'menus/create',        array('middleware' => 'auth', 'uses' => 'Menus@create'));
    Route::get( 'menus/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Menus@edit'));
    Route::post('menus/{id}',          array('middleware' => 'auth', 'uses' => 'Menus@update'));
    Route::post('menus/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Menus@destroy'));

    Route::get( 'menus/{id}',                        array('middleware' => 'auth', 'uses' => 'Menus@items'));
    Route::get( 'menus/{id}/items/{itemId}',         array('middleware' => 'auth', 'uses' => 'Menus@viewItem'));
    Route::post('menus/{id}/items/{itemId}',         array('middleware' => 'auth', 'uses' => 'Menus@updateItem'));
    Route::post('menus/{id}/items/{itemId}/destroy', array('middleware' => 'auth', 'uses' => 'Menus@deleteItem'));

    // Order the Menu Items via AJAX.
    Route::post('menus/{id}/items/order', array('middleware' => 'auth', 'uses' => 'Menus@order'));

    //
    Route::get( 'content/sample',  array('middleware' => 'auth', 'uses' => 'Posts@sample'));

    // The Posts CRUD.
    Route::get( 'content/create/{type}', array('middleware' => 'auth', 'uses' => 'Posts@create'));
    Route::get( 'content/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Posts@edit'));
    Route::post('content/{id}',          array('middleware' => 'auth', 'uses' => 'Posts@update'))->where('id', '\d+');
    Route::post('content/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Posts@destroy'))->where('id', '\d+');

    Route::post('content/{id}/tags', array('middleware' => 'auth', 'uses' => 'Posts@addTags'))->where('id', '\d+');

    Route::post('content/{postId}/tags/{tagId}/detach', array(
        'middleware' => 'auth',
        'uses' => 'Posts@detachTag',

        // The route patterns.
        'where' => array(
            'id'    => '\d+',
            'tagId' => '\d+',
        ),
    ));

    // The Taxonomies listings.
    Route::get('content/categories',    array('middleware' => 'auth', 'uses' => 'Taxonomies@index'));
    Route::get('content/tags',          array('middleware' => 'auth', 'uses' => 'Taxonomies@tags'));

    Route::get('content/{type}/{slug}', array('middleware' => 'auth', 'uses' => 'Posts@taxonomy'));

    // The Posts listing.
    Route::get('content/{type?}',       array('middleware' => 'auth', 'uses' => 'Posts@index'));

    // The Taxonomies CRUD.
    Route::post('taxonomies',               array('middleware' => 'auth', 'uses' => 'Taxonomies@store'));
    Route::post('taxonomies/{id}',          array('middleware' => 'auth', 'uses' => 'Taxonomies@update'));
    Route::post('taxonomies/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Taxonomies@destroy'));

    // For AJAX.
    Route::get('taxonomies/{id}/{parent}', array(
        'middleware' => 'auth',
        'uses'       => 'Taxonomies@categories',

        'where' => array(
            'id'     => '\d+',
            'parent' => '\d+',
        ),
    ));
});
