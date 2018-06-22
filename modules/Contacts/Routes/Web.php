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
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin', 'where' => $wheres), function ()
{
    Route::get( 'contacts',               'Contacts@index');
    Route::get( 'contacts/create',        'Contacts@create');
    Route::post('contacts',               'Contacts@store');
    Route::get( 'contacts/{id}',          'Contacts@show');
    Route::get( 'contacts/{id}/edit',     'Contacts@edit');
    Route::post('contacts/{id}',          'Contacts@update');
    Route::post('contacts/{id}/destroy',  'Contacts@destroy');

    // The Field Groups and their Field Items.
    Route::get( 'contacts/{cid}/field-groups',              'FieldGroups@index');
    Route::post('contacts/{cid}/field-groups',              'FieldGroups@store');
    Route::post('contacts/{cid}/field-groups/{id}/update',  'FieldGroups@update');
    Route::post('contacts/{cid}/field-groups/{id}/destroy', 'FieldGroups@destroy');

    Route::get( 'contacts/field-groups/{gid}/items/create',       'FieldItems@create');
    Route::post('contacts/field-groups/{gid}/items',              'FieldItems@store');
    Route::get( 'contacts/field-groups/{gid}/items/{id}',         'FieldItems@show');
    Route::get( 'contacts/field-groups/{gid}/items/{id}/edit',    'FieldItems@edit');
    Route::post('contacts/field-groups/{gid}/items/{id}',         'FieldItems@update');
    Route::post('contacts/field-groups/{gid}/items/{id}/destroy', 'FieldItems@destroy');

    // The Contact Messages.
    Route::get( 'contacts/{cid}/messages',        'Messages@index');
    Route::get( 'contacts/{cid}/messages/search', 'Messages@search');

    Route::get( 'contacts/{cid}/messages/{id}',   'Messages@show');
    Route::post('contacts/{cid}/messages/{id}',   'Messages@destroy');
});
