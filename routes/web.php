<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => '/api/v1/disk/'], function () {

    Route::group(['middleware' => ['auth', 'auth.basic']], function () {

        // Routes for disk browser
        Route::post('directories', 'Api\DirectoryController@index');
        Route::post('directory/store', 'Api\DirectoryController@store');
        Route::post('directory/destroy', 'Api\DirectoryController@destroy');
        Route::post('files', 'Api\FileController@index');
        Route::post('file/store', 'Api\FileController@store');
        Route::post('search', 'Api\DiskController@search');

   });
});