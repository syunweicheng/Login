<?php

use Illuminate\Http\Request;

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

Route::middleware(['api', 'throttle:10,1'])->namespace('AuthJWT')->prefix('v1')->group(function () {
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/logout', 'AuthController@logout');
});

Route::middleware(['jwt', 'auth:member', 'api', 'throttle:20,1'])->prefix('v1')->group(function () {
    /** 會員資料 Member*/
    Route::group(['prefix' => 'member'], function () {
        Route::get('/', 'MembersController@me')->name('jwt.member.me');                                   //顯示會員資料\
    });
});