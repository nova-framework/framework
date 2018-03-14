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

// The administration group's wheres.
$wheres = array(
    'id'  => '\d+',
    'cid' => '\d+',
    'gid' => '\d+'
);

// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'where' => $wheres), function ()
{
    Route::get( 'contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@index'));
    Route::get( 'contacts/create',        array('middleware' => 'auth', 'uses' => 'Contacts@create'));
    Route::post('contacts',               array('middleware' => 'auth', 'uses' => 'Contacts@store'));
    Route::get( 'contacts/{id}',          array('middleware' => 'auth', 'uses' => 'Contacts@show'));
    Route::get( 'contacts/{id}/edit',     array('middleware' => 'auth', 'uses' => 'Contacts@edit'));
    Route::post('contacts/{id}',          array('middleware' => 'auth', 'uses' => 'Contacts@update'));
    Route::post('contacts/{id}/destroy',  array('middleware' => 'auth', 'uses' => 'Contacts@destroy'));

    // The Field Groups and their Field Items.
    Route::get( 'contacts/{cid}/field-groups', array('middleware' => 'auth', 'uses' => 'FieldGroups@index'));
    Route::post('contacts/{cid}/field-groups', array('middleware' => 'auth', 'uses' => 'FieldGroups@store'));

    Route::post('contacts/{cid}/field-groups/{id}/update',  array('middleware' => 'auth', 'uses' => 'FieldGroups@update'));
    Route::post('contacts/{cid}/field-groups/{id}/destroy', array('middleware' => 'auth', 'uses' => 'FieldGroups@destroy'));

    Route::get( 'contacts/field-groups/{gid}/items/create',       array('middleware' => 'auth', 'uses' => 'FieldItems@create'));
    Route::post('contacts/field-groups/{gid}/items',              array('middleware' => 'auth', 'uses' => 'FieldItems@store'));
    Route::get( 'contacts/field-groups/{gid}/items/{id}/edit',    array('middleware' => 'auth', 'uses' => 'FieldItems@edit'));
    Route::post('contacts/field-groups/{gid}/items/{id}',         array('middleware' => 'auth', 'uses' => 'FieldItems@update'));
    Route::post('contacts/field-groups/{gid}/items/{id}/destroy', array('middleware' => 'auth', 'uses' => 'FieldItems@destroy'));

    // The Contact Messages.
    Route::get( 'contacts/{cid}/messages',        array('middleware' => 'auth', 'uses' => 'Messages@index'));
    Route::get( 'contacts/{cid}/messages/search', array('middleware' => 'auth', 'uses' => 'Messages@search'));

    Route::get( 'contacts/{cid}/messages/{id}',   array('middleware' => 'auth', 'uses' => 'Messages@show'));
    Route::post('contacts/{cid}/messages/{id}',   array('middleware' => 'auth', 'uses' => 'Messages@destroy'));
});
