<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => '/api/v1/'], function () {

    Route::group(['middleware' => ['auth.basic']], function () {

        Route::post('directories', 'DirectoryController@index');
        Route::post('files', 'FileController@index');

    });
});

// Authentication routes...
Route::group(['middleware' => 'guest'], function()
{
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::post('login', 'Auth\AuthController@postLogin');

});

// Authentication routes...
Route::group(['middleware' => 'auth'], function()
{

    Route::get('', function () {

        return view('welcome');
    });
});

Route::get('logout', 'Auth\AuthController@getLogout');
