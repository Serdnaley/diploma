<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group([
    'namespace' => 'API',
    'middleware' => []
], function () {

    // Авторизация
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::get('refresh', 'AuthController@refresh');

        Route::group(['middleware' => 'auth:api'], function(){
            Route::get('user', 'AuthController@user');
            Route::any('logout', 'AuthController@logout');
        });
        Route::group(['middleware' => 'auth:api'], function(){
            // Users
            Route::get('users', 'UserController@index')->middleware('is.admin');
            Route::get('users/{id}', 'UserController@show')->middleware('is.admin.or.self');
        });
    });

    Route::group([
        'middleware' => 'api',
        'prefix' => 'password'
    ], function () {
        Route::post('create', 'PasswordResetController@create');
        Route::get('find/{token}', 'PasswordResetController@find');
        Route::post('reset', 'PasswordResetController@reset');
    });

    // Редактируем профиль
    Route::group(['prefix' => 'file'], function (){
        Route::post('store', 'FileController@store');
    });
});