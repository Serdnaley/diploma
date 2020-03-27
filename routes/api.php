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
], function () {

    // Авторизация
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::get('refresh', 'AuthController@refresh');

        Route::group(['middleware' => 'auth:api'], function(){
            Route::get('user', 'AuthController@user');
            Route::any('logout', 'AuthController@logout');
        });
    });

    Route::apiResource('categories', 'UserCategoryController');
    Route::apiResource('report', 'UserCategoryController');


    // Редактируем профиль
    Route::group(['prefix' => 'file'], function (){
        Route::post('store', 'UserFileController@store');
    });
});
