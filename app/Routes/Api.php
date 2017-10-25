<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Nova\Http\Request;


Route::get('user', array('middleware' => 'auth:api', function (Request $request)
{
    return $request->user();
}));
