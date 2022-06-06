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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login','App\Http\Controllers\Auth\LoginController@apiLogin');

Route::prefix('stadium')->group(function () {
    Route::get('/','App\Http\Controllers\StadiumController@list');
    Route::get('/{id}','App\Http\Controllers\StadiumController@getById');
    Route::post('','App\Http\Controllers\StadiumController@create');
    Route::put('/{id}','App\Http\Controllers\StadiumController@update');
});

Route::prefix('teams')->group(function () {
    Route::get('/','App\Http\Controllers\TeamController@list');
    Route::get('/{id}','App\Http\Controllers\TeamController@getById');
    Route::post('/','App\Http\Controllers\TeamController@create');
    Route::put('/{id}','App\Http\Controllers\TeamController@update');
});


