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

use Modules\Content\Support\Facades\PostType;


// The Media Files serving.
Route::get('content/media/serve/{name}', 'Attachments@serve');

// The Content dispatching.
Route::get('content/archive/{year}/{month}', array(
    'uses' => 'Content@archive',

    'where' => array(
        'year'  => '\d+',
        'month' => '\d+',
    ),
));

//Route::get('/', 'Content@homepage');

Route::get('content/search', 'Content@search');

Route::get('content/{type}/{slug}', array('uses' => 'Content@taxonomy'))->where('type', '(category|tag)');

Route::get('content/{slug?}', array('uses' => 'Content@index'))->where('slug', '(.*)');

// Content unlocking for the Password Protected pages and posts.
Route::post('content/{id}', 'Content@unlock')->where('id', '\d+');

// Comments.
Route::post('content/{id}/comment', 'Comments@store')->where('id', '\d+');


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'where' => array('id' => '\d+')), function ()
{
    // The Media CRUD.
    Route::get( 'media',                array('middleware' => 'auth', 'uses' => 'Attachments@index'));
    Route::post('media/update/{field}', array('middleware' => 'auth', 'uses' => 'Attachments@update'));
    Route::post('media/delete',         array('middleware' => 'auth', 'uses' => 'Attachments@destroy'));

    Route::post('media/upload',         array('middleware' => 'auth', 'uses' => 'Attachments@upload'));
    Route::get( 'media/uploaded',       array('middleware' => 'auth', 'uses' => 'Attachments@uploaded'));

    // The Blocks positions.
    Route::get( 'blocks', array('middleware' => 'auth', 'uses' => 'Blocks@index'));
    Route::post('blocks', array('middleware' => 'auth', 'uses' => 'Blocks@order'));

    // The Menus CRUD.
    Route::get( 'menus',               array('middleware' => 'auth', 'uses' => 'Menus@index'));
    Route::post('menus',               array('middleware' => 'auth', 'uses' => 'Menus@store'));
    Route::post('menus/{id}',          array('middleware' => 'auth', 'uses' => 'Menus@update'));
    Route::post('menus/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Menus@destroy'));

    Route::get( 'menus/{id}',                        array('middleware' => 'auth', 'uses' => 'Menus@items'));
    Route::post('menus/{id}/post',                   array('middleware' => 'auth', 'uses' => 'Menus@addPost'));
    Route::post('menus/{id}/category',               array('middleware' => 'auth', 'uses' => 'Menus@addCategory'));
    Route::post('menus/{id}/custom',                 array('middleware' => 'auth', 'uses' => 'Menus@addCustom'));
    Route::post('menus/{id}/items',                  array('middleware' => 'auth', 'uses' => 'Menus@itemsOrder'));
    Route::post('menus/{id}/items/{itemId}',         array('middleware' => 'auth', 'uses' => 'Menus@updateItem'));
    Route::post('menus/{id}/items/{itemId}/destroy', array('middleware' => 'auth', 'uses' => 'Menus@deleteItem'));

    // The Comments CRUD.
    Route::get( 'comments',                array('middleware' => 'auth', 'uses' => 'Comments@index'));
    Route::get( 'comments/{id}',           array('middleware' => 'auth', 'uses' => 'Comments@load'));
    Route::post('comments/{id}',           array('middleware' => 'auth', 'uses' => 'Comments@update'));
    Route::post('comments/{id}/destroy',   array('middleware' => 'auth', 'uses' => 'Comments@destroy'));

    Route::post('comments/{id}/approve',   array('middleware' => 'auth', 'uses' => 'Comments@approve'));
    Route::post('comments/{id}/unapprove', array('middleware' => 'auth', 'uses' => 'Comments@unapprove'));

    // The Posts CRUD.
    Route::get('content/create/{type}',  array('middleware' => 'auth', 'uses' => 'Posts@create'))
        ->where('type', '(' .implode('|', PostType::getNames()) .')');

    //
    Route::get( 'content/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Posts@edit'));
    Route::post('content/{id}',          array('middleware' => 'auth', 'uses' => 'Posts@update'));
    Route::post('content/{id}/restore',  array('middleware' => 'auth', 'uses' => 'Posts@restore'));
    Route::post('content/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Posts@destroy'));

    Route::get('content/{id}/revisions', array('middleware' => 'auth', 'uses' => 'Posts@revisions'));

    Route::post('content/{id}/tags', array('middleware' => 'auth', 'uses' => 'Posts@addTags'));

    Route::post('content/{id}/tags/{tagId}/detach', array('middleware' => 'auth', 'uses' => 'Posts@detachTag'))
        ->where('tagId', '\d+');

    // The Taxonomies listings.
    Route::get('content/categories',    array('middleware' => 'auth', 'uses' => 'Taxonomies@index'));
    Route::get('content/tags',          array('middleware' => 'auth', 'uses' => 'Taxonomies@tags'));

    Route::get('content/{type}/{slug}', array('middleware' => 'auth', 'uses' => 'Posts@taxonomy'))
        ->where('type', '(category|tag)');

    // The Posts listing.
    Route::get('content/{slug?}', array('middleware' => 'auth', 'uses' => 'Posts@index'))
        ->where('type', '(' .implode('|', PostType::getSlugs()) .')');

    // The Taxonomies CRUD.
    Route::post('taxonomies',              array('middleware' => 'auth', 'uses' => 'Taxonomies@store'));
    Route::post('taxonomies/{id}',         array('middleware' => 'auth', 'uses' => 'Taxonomies@update'));
    Route::post('taxonomies/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Taxonomies@destroy'));

    // For AJAX.
    Route::get('taxonomies/{id}/{parent}', array('middleware' => 'auth', 'uses' => 'Taxonomies@categories'))
        ->where('parent', '\d+');
});
