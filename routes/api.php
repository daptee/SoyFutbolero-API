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

Route::get('/user', function (Request $request) {
    return auth()->user();
});

Route::post('login','App\Http\Controllers\Auth\LoginController@apiLogin');

Route::prefix('stadium')->group(function () {
    Route::get('/','App\Http\Controllers\StadiumController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\StadiumController@getById')->middleware('api-auth');
    Route::post('','App\Http\Controllers\StadiumController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\StadiumController@update')->middleware('api-auth');
});

Route::prefix('teams')->group(function () {
    Route::get('/','App\Http\Controllers\TeamController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\TeamController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\TeamController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\TeamController@update')->middleware('api-auth');
});

Route::prefix('user')->group(function () {
    Route::get('/','App\Http\Controllers\UserController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\UserController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\UserController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\UserController@update')->middleware('api-auth');
});

Route::prefix('turnament')->group(function () {
    Route::get('/','App\Http\Controllers\TurnamentController@list');
    Route::get('/{id}','App\Http\Controllers\TurnamentController@getById');
    Route::post('/','App\Http\Controllers\TurnamentController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\TurnamentController@update');
    Route::delete('/{id}','App\Http\Controllers\TurnamentController@delete');
    Route::post('/change-state','App\Http\Controllers\TurnamentController@changeState')->middleware('api-auth');
});

Route::prefix('stage')->group(function () {
    Route::get('/','App\Http\Controllers\StageController@list');
});

Route::prefix('turnament-state')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentStateController@list');
});

Route::prefix('turnament-type')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentTypeController@list');
});

Route::get('/turnament-group','App\Http\Controllers\TurnamentController@listWithGroup');

Route::prefix('group')->group(function () {
    Route::get('/','App\Http\Controllers\GroupController@list');
    Route::get('/{id}','App\Http\Controllers\GroupController@getById');
    Route::post('/','App\Http\Controllers\GroupController@create');
    Route::put('/{id}','App\Http\Controllers\GroupController@update');
    Route::delete('/{id}','App\Http\Controllers\GroupController@delete');
});


