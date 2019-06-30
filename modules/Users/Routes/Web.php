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

// The default Auth Routes.
Route::get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@index'));
Route::post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@process'));
Route::post('logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));


// The Public Area Routes.
Route::group(array('middleware' => 'guest'), function ()
{
    // The One-Time Authentication.
    Route::get( 'authorize', 'LoginTokens@index');
    Route::post('authorize', 'LoginTokens@process');

    Route::get('authorize/{hash}/{time}/{token}', 'LoginTokens@login');

    // The Password Reminder.
    Route::get( 'password/remind', 'PasswordReminders@remind');
    Route::post('password/remind', 'PasswordReminders@postRemind');

    // The Password Reset.
    Route::post('password/reset', 'PasswordReminders@postReset');

    Route::get('password/reset/{hash}/{time}/{token}', 'PasswordReminders@reset');

    // The Account Registration.
    Route::get( 'register',        'Registrar@create');
    Route::post('register',        'Registrar@store');
    Route::get( 'register/status', 'Registrar@status');
    Route::get( 'register/verify', 'Registrar@verify');
    Route::post('register/verify', 'Registrar@verifyPost');

    Route::get('register/{hash}/{time}/{token}', 'Registrar@tokenVerify');
});


// The Frontend Area Routes.
Route::group(array('middleware' => 'auth'), function ()
{
    // The User's Account.
    Route::get( 'account',         'Account@index');
    Route::post('account',         'Account@update');
    Route::post('account/picture', 'Account@picture');
});


// The Adminstration Routes.
Route::group(array('prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'), function ()
{
    // The Custom Fields CRUD.
    Route::get( 'users/fields',              'FieldItems@index');
    Route::get( 'users/fields/create',       'FieldItems@create');
    Route::post('users/fields',              'FieldItems@store');
    Route::get( 'users/fields/{id}',         'FieldItems@show')->where('id', '\d+');
    Route::get( 'users/fields/{id}/edit',    'FieldItems@edit')->where('id', '\d+');
    Route::post('users/fields/{id}',         'FieldItems@update')->where('id', '\d+');
    Route::post('users/fields/{id}/destroy', 'FieldItems@destroy')->where('id', '\d+');

    // Server Side Processor for Users DataTable.
    Route::post('users/data', 'Users@data');

    // The Users CRUD.
    Route::get( 'users',              'Users@index');
    Route::get( 'users/create',       'Users@create');
    Route::post('users',              'Users@store');
    Route::get( 'users/{id}',         'Users@show')->where('id', '\d+');
    Route::get( 'users/{id}/edit',    'Users@edit')->where('id', '\d+');
    Route::post('users/{id}',         'Users@update')->where('id', '\d+');
    Route::post('users/{id}/destroy', 'Users@destroy')->where('id', '\d+');
});
