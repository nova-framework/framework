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
use Modules\Content\Support\Facades\TaxonomyType;


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

//
$types = TaxonomyType::getRouteSlugs(false);

Route::get('content/{type}/{slug}', array('uses' => 'Content@taxonomy'))->where('type', '(' .implode('|', $types) .')');

Route::get('content/{slug?}', array('uses' => 'Content@index'))->where('slug', '(.*)');

// Content unlocking for the Password Protected pages and posts.
Route::post('content/{id}', 'Content@unlock')->where('id', '\d+');

// Comments.
Route::post('content/{id}/comment', 'Comments@store')->where('id', '\d+');


// The administration group's wheres.
$wheres = array(
    'id'  => '\d+',
);

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin', 'where' => $wheres), function ()
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
    $types = PostType::getRouteSlugs(false);

    Route::get('content/create/{type}',  'Posts@create')->where('type', '(' .implode('|', $types) .')');

    //
    Route::get( 'content/{id}/edit',     'Posts@edit');
    Route::post('content/{id}',          'Posts@update');
    Route::post('content/{id}/restore',  'Posts@restore');
    Route::post('content/{id}/destroy',  'Posts@destroy');

    Route::get('content/{id}/revisions', 'Posts@revisions');

    Route::post('content/{id}/tags', 'Posts@addTags');

    Route::post('content/{id}/tags/{tagId}/detach', 'Posts@detachTag')->where('tagId', '\d+');

    // The Posts listing.
    $types = PostType::getRouteSlugs();

    Route::get('content/{type}', 'Posts@index')->where('type', '(' .implode('|', $types) .')');

    // The Taxonomies CRUD.
    Route::post('taxonomies',              'Taxonomies@store');
    Route::post('taxonomies/{id}',         'Taxonomies@update');
    Route::post('taxonomies/{id}/destroy', 'Taxonomies@destroy');

    // For AJAX.
    Route::get('taxonomies/{id}/{parentId}', 'Taxonomies@categories')->where('parentId', '\d+');

    //
    $types = TaxonomyType::getRouteSlugs(false);

    Route::get('taxonomies/{type}/{slug}', 'Posts@taxonomy')->where('type', '(' .implode('|', $types) .')');

    // The Taxonomies listings.
    Route::get('taxonomies/categories',    'Taxonomies@index');
    Route::get('taxonomies/tags',          'Taxonomies@tags');
});
