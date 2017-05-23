<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The default Auth Routes.
$router->get( 'auth/login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
$router->post('auth/login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
$router->get( 'auth/logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The Account Registration.
$router->get( 'auth/register',                 array('middleware' => 'guest', 'uses' => 'Registrar@create'));
$router->post('auth/register',                 array('middleware' => 'guest', 'uses' => 'Registrar@store'));
$router->get( 'auth/register/verify/{token?}', array('middleware' => 'guest', 'uses' => 'Registrar@verify'));
$router->get( 'auth/register/status',          array('middleware' => 'guest', 'uses' => 'Registrar@status'));

// The Password Remind.
$router->get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@remind'));
$router->post('password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@postRemind'));

// The Password Reset.
$router->get( 'password/reset/{token}', array('middleware' => 'guest', 'uses' => 'Authorize@reset'));
$router->post('password/reset',         array('middleware' => 'guest', 'uses' => 'Authorize@postReset'));


// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'namespace' => 'Admin'), function($router)
{
    // The User's Profile.
    $router->get( 'profile', array('middleware' => 'auth', 'uses' => 'Profile@index'));
    $router->post('profile', array('middleware' => 'auth', 'uses' => 'Profile@update'));

    // The Users Search.
    $router->post('users/search', array('middleware' => 'auth', 'uses' => 'Users@search'));

    // Server Side Processor for Users DataTable.
    $router->post('users/data', array('middleware' => 'auth', 'uses' => 'Users@data'));

    // The Users CRUD.
    $router->get( 'users',              array('middleware' => 'auth', 'uses' => 'Users@index'));
    $router->get( 'users/create',       array('middleware' => 'auth', 'uses' => 'Users@create'));
    $router->post('users',              array('middleware' => 'auth', 'uses' => 'Users@store'));
    $router->get( 'users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@show'));
    $router->get( 'users/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Users@edit'));
    $router->post('users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@update'));
    $router->post('users/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Users@destroy'));

    // Server Side Processor for Roles DataTable.
    $router->post('roles/data', array('middleware' => 'auth', 'uses' => 'Roles@data'));

    // The Roles CRUD.
    $router->get( 'roles',              array('middleware' => 'auth', 'uses' => 'Roles@index'));
    $router->get( 'roles/create',       array('middleware' => 'auth', 'uses' => 'Roles@create'));
    $router->post('roles',              array('middleware' => 'auth', 'uses' => 'Roles@store'));
    $router->get( 'roles/{id}',         array('middleware' => 'auth', 'uses' => 'Roles@show'));
    $router->get( 'roles/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Roles@edit'));
    $router->post('roles/{id}',         array('middleware' => 'auth', 'uses' => 'Roles@update'));
    $router->post('roles/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Roles@destroy'));
});
