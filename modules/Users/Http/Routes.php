<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define static routes. */

// The default Auth Routes.
$router->get( 'login',  array('middleware' => 'guest', 'uses' => 'Authorize@login'));
$router->post('login',  array('middleware' => 'guest', 'uses' => 'Authorize@postLogin'));
$router->get( 'logout', array('middleware' => 'auth',  'uses' => 'Authorize@logout'));

// The Password Remind.
$router->get( 'password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@remind'));
$router->post('password/remind', array('middleware' => 'guest', 'uses' => 'Authorize@postRemind'));

// The Password Reset.
$router->get( 'password/reset/{token}', array('middleware' => 'guest', 'uses' => 'Authorize@reset'));
$router->post('password/reset',         array('middleware' => 'guest', 'uses' => 'Authorize@postReset'));

// The Account Registration.
$router->get( 'register',                 array('middleware' => 'guest', 'uses' => 'Registrar@create'));
$router->post('register',                 array('middleware' => 'guest', 'uses' => 'Registrar@store'));
$router->get( 'register/verify/{token?}', array('middleware' => 'guest', 'uses' => 'Registrar@verify'));
$router->get( 'register/status',          array('middleware' => 'guest', 'uses' => 'Registrar@status'));


// The Adminstration Routes.
$router->group(array('prefix' => 'admin', 'namespace' => 'Admin'), function($router)
{
    // The User's Profile.
    $router->get( 'profile', array('middleware' => 'auth',      'uses' => 'Profile@index'));
    $router->post('profile', array('middleware' => 'auth', 'uses' => 'Profile@update'));

    // The Users Search.
    $router->post('users/search', array('middleware' => 'auth', 'uses' => 'Users@search'));

    // The Users CRUD.
    $router->get( 'users',              array('middleware' => 'auth', 'uses' => 'Users@index'));
    $router->get( 'users/create',       array('middleware' => 'auth', 'uses' => 'Users@create'));
    $router->post('users',              array('middleware' => 'auth', 'uses' => 'Users@store'));
    $router->get( 'users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@show'));
    $router->get( 'users/{id}/edit',    array('middleware' => 'auth', 'uses' => 'Users@edit'));
    $router->post('users/{id}',         array('middleware' => 'auth', 'uses' => 'Users@update'));
    $router->post('users/{id}/destroy', array('middleware' => 'auth', 'uses' => 'Users@destroy'));
});
