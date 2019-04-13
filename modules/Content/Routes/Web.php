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

use Modules\Content\Support\Facades\PostType as Posts;
use Modules\Content\Support\Facades\TaxonomyType as Taxonomies;


// The Media Files serving.
Route::get('content/media/serve/{name}', 'Attachments@serve');


// The Content dispatching.
Route::paginate('content/archive/{year}/{month}', array(
    'uses' => 'Content@archive',

    'where' => array(
        'year'  => '\d+',
        'month' => '\d+',
    ),
));

//Route::paginate('/', 'Content@homepage');

Route::paginate('content', array('uses' => 'Content@homepage'));

Route::paginate('content/search', 'Content@search');

//
Route::paginate('content/{type}/{slug}', array('uses' => 'Content@taxonomy'))->where('type', Taxonomies::routePattern(false));

Route::get('content/{slug}', array('uses' => 'Content@show'));

// Content unlocking for the Password Protected pages and posts.
Route::post('content/{id}', 'Content@unlock')->where('id', '\d+');

// Comments.
Route::post('content/{id}/comment', 'Comments@store')->where('id', '\d+');


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin', 'where' => array('id'  => '\d+')), function ()
{
    // The Media CRUD.
    Route::get( 'media',                'Attachments@index');
    Route::post('media/update/{field}', 'Attachments@update');
    Route::post('media/delete',         'Attachments@destroy');

    Route::post('media/upload',         'Attachments@upload');
    Route::get( 'media/uploaded',       'Attachments@uploaded');

    // The Blocks positions.
    Route::get( 'blocks', 'Blocks@index');
    Route::post('blocks', 'Blocks@order');

    // The Menus CRUD.
    Route::get( 'menus',               'Menus@index');
    Route::post('menus',               'Menus@store');
    Route::post('menus/{id}',          'Menus@update');
    Route::post('menus/{id}/destroy',  'Menus@destroy');

    Route::get( 'menus/{id}',                        'Menus@items');
    Route::post('menus/{id}/post',                   'Menus@addPost');
    Route::post('menus/{id}/category',               'Menus@addCategory');
    Route::post('menus/{id}/custom',                 'Menus@addCustom');
    Route::post('menus/{id}/items',                  'Menus@itemsOrder');
    Route::post('menus/{id}/items/{itemId}',         'Menus@updateItem');
    Route::post('menus/{id}/items/{itemId}/destroy', 'Menus@deleteItem');

    // The Comments CRUD.
    Route::get( 'comments',                'Comments@index');
    Route::get( 'comments/{id}',           'Comments@load');
    Route::post('comments/{id}',           'Comments@update');
    Route::post('comments/{id}/destroy',   'Comments@destroy');

    Route::post('comments/{id}/approve',   'Comments@approve');
    Route::post('comments/{id}/unapprove', 'Comments@unapprove');

    // The Posts CRUD.
    Route::get('content/create/{type}',  'Posts@create')->where('type', Posts::routePattern(false));

    //
    Route::get( 'content/{id}/edit',    'Posts@edit');
    Route::post('content/{id}',         'Posts@update');
    Route::post('content/{id}/restore', 'Posts@restore');
    Route::post('content/{id}/destroy', 'Posts@destroy');

    Route::get( 'content/{id}/revisions', 'Posts@revisions');

    Route::post('content/{id}/tags', 'Posts@addTags');

    Route::post('content/{id}/tags/{tagId}/detach', 'Posts@detachTag')->where('tagId', '\d+');

    // The Posts listing.
    Route::get('content/{type}', 'Posts@index')->where('type', Posts::routePattern(true));

    //
    Route::get('taxonomies/{type}/{slug}', 'Posts@taxonomy')->where('type', Taxonomies::routePattern(false));

    // The Taxonomies CRUD.
    Route::post('taxonomies',              'Taxonomies@store');
    Route::post('taxonomies/{id}',         'Taxonomies@update');
    Route::post('taxonomies/{id}/destroy', 'Taxonomies@destroy');

    // The Taxonomies listings.
    Route::get('taxonomies/{type}', 'Taxonomies@index')->where('type', Taxonomies::routePattern(true));

    // For AJAX.
    Route::get('taxonomies/{id}/{parentId}', 'Taxonomies@data')->where('parentId', '\d+');
});
