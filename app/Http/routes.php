<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api', 'middleware' => ['cors', 'api']], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function(){
            Route::post('register', 'AuthController@register');
            Route::post('login', 'AuthController@login');
            Route::post('logout', 'AuthController@logout');
            Route::get('checkEmail/{email}', 'AuthController@checkEmail');
        });
        Route::group(['middleware' => ['jwt.auth']], function () {
            Route::group(['prefix' => 'bucket', 'namespace' => 'Bucket'], function(){
                Route::post('create', 'BucketController@store');
                Route::post('list', 'BucketController@index');
                Route::delete('delete/{bucket}', 'BucketController@destroy');
            });
            Route::group(['prefix' => 'file', 'namespace' => 'File'], function(){
                Route::get('list/{bucket}', 'FileController@index');
                Route::post('create', 'FileController@store');
                Route::post('rename', 'FileController@rename');
                Route::get('get/{bucket}/{key}', 'FileController@getFile')->where('key', '(.*)');
                Route::delete('delete/{bucket}/{key}', 'FileController@destroy')->where('key', '(.*)');
            });
            Route::group(['prefix' => 'folder', 'namespace' => 'Folder'], function(){
                Route::post('create', 'FolderController@store');
                Route::delete('delete/{bucket}/{key}', 'FolderController@destroy')->where('key', '(.*)');
            });

        });
    });
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
