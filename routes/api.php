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
    Route::post('/edit','App\Http\Controllers\TeamController@update')->middleware('api-auth');
});

Route::prefix('user')->group(function () {
    Route::get('/','App\Http\Controllers\UserController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\UserController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\UserController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\UserController@update')->middleware('api-auth');
});

Route::prefix('turnament')->group(function () {
    Route::get('/','App\Http\Controllers\TurnamentController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\TurnamentController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\TurnamentController@create')->middleware('api-auth');
    Route::post('/edit','App\Http\Controllers\TurnamentController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\TurnamentController@delete')->middleware('api-auth');
    Route::post('/change-state','App\Http\Controllers\TurnamentController@changeState')->middleware('api-auth');
    Route::get('/stage/{id}','App\Http\Controllers\TurnamentController@getTournamentStages')->middleware('api-auth');
    Route::get('/team/{id}','App\Http\Controllers\TurnamentController@getTournamentTeams')->middleware('api-auth');
    Route::get('/users/{id}','App\Http\Controllers\TurnamentController@getTournamentUsers')->middleware('api-auth');
    Route::post('/winners/{id}','App\Http\Controllers\TurnamentController@setWinners')->middleware('api-auth');
    Route::put('/winners/{id}','App\Http\Controllers\TurnamentController@updateWinners')->middleware('api-auth');
});

Route::prefix('stage')->group(function () {
    Route::get('/','App\Http\Controllers\StageController@list')->middleware('api-auth');
});

Route::prefix('turnament-state')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentStateController@list')->middleware('api-auth');
});

Route::prefix('turnament-type')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentTypeController@list')->middleware('api-auth');
});

Route::get('/turnament-group','App\Http\Controllers\TurnamentController@listWithGroup')->middleware('api-auth');

Route::prefix('group')->group(function () {
    Route::get('/','App\Http\Controllers\GroupController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\GroupController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\GroupController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\GroupController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\GroupController@delete')->middleware('api-auth');
});

Route::prefix('match')->group(function () {
    Route::get('/torrnament','App\Http\Controllers\MatchController@list')->middleware('api-auth');
    Route::get('/torrnament/{tournament_id}','App\Http\Controllers\MatchController@tournamentMatchsById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\MatchController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\MatchController@update')->middleware('api-auth');
});


Route::prefix('user-tournament')->group(function () {
    Route::get('/','App\Http\Controllers\UserTournametController@list')->middleware('api-auth');
    Route::post('','App\Http\Controllers\UserTournametController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\UserTournametController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\UserTournametController@delete')->middleware('api-auth');
});

Route::prefix('notification')->group(function () {
    Route::get('/','App\Http\Controllers\NotificationController@list')->middleware('api-auth');
    Route::post('','App\Http\Controllers\NotificationController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\NotificationController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\NotificationController@delete')->middleware('api-auth');
    Route::post('read/{id}','App\Http\Controllers\NotificationController@readNotification')->middleware('api-auth');
});
