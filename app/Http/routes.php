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

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function(){
            Route::post('register', 'AuthController@register');
            Route::post('login', 'AuthController@login');
            Route::post('checkEmail', 'AuthController@checkEmail');
        });
        Route::group(['middleware' => ['jwt.auth']], function () {
            Route::post('aaa', function(){
                return JWTAuth::parseToken()->authenticate();
            });
        });
    });
});


Route::group(['prefix' => 'demo', 'as' => 'demo.'], function () {
    // Demo
    Route::post('create', ['as' => 'create', 'uses' => 'Demo\DemoController@store']);
    Route::get('read', ['as' => 'read', 'uses' => 'Demo\DemoController@index']);
    Route::post('update', ['as' => 'update', 'uses' => 'Demo\DemoController@update']);
    Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'Demo\DemoController@destroy']);

});
Route::post('/test', 'Test\TestController@index');
// Route::post('/test', ['as' => 'test.index', 'uses' => 'uses' => 'Test\TestController@index']);
// Route::resource('demo', 'Demo\DemoController');


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
