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
Route::post('adminLogin','App\Http\Controllers\Auth\LoginController@apiAdminLogin');

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
    Route::post('/edit-profile','App\Http\Controllers\UserController@editProfile')->middleware('api-auth');
    Route::post('/register','App\Http\Controllers\UserController@register');
    Route::post('/reset-password','App\Http\Controllers\UserController@resetPassword');
});

Route::prefix('tournament')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\TournamentController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\TournamentController@create')->middleware('api-auth');
    Route::post('/edit','App\Http\Controllers\TournamentController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\TournamentController@delete')->middleware('api-auth');
    Route::post('/change-state','App\Http\Controllers\TournamentController@changeState')->middleware('api-auth');
    Route::get('/stage/{id}','App\Http\Controllers\TournamentController@getTournamentStages')->middleware('api-auth');
    Route::get('/team/{id}','App\Http\Controllers\TournamentController@getTournamentTeams')->middleware('api-auth');
    Route::get('/users/{id}','App\Http\Controllers\TournamentController@getTournamentUsers')->middleware('api-auth');
    Route::post('/winners/{id}','App\Http\Controllers\TournamentController@setWinners')->middleware('api-auth');
    Route::put('/winners/{id}','App\Http\Controllers\TournamentController@updateWinners')->middleware('api-auth');
    Route::get('/all-data/{id}','App\Http\Controllers\TournamentController@getAllDataById')->middleware('api-auth');
});

Route::prefix('stage')->group(function () {
    Route::get('/','App\Http\Controllers\StageController@list')->middleware('api-auth');
});

Route::prefix('tournament-state')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentStateController@list')->middleware('api-auth');
});

Route::prefix('tournament-type')->group(function () {
    Route::get('/','App\Http\Controllers\TournamentTypeController@list')->middleware('api-auth');
});

Route::get('/tournament-group','App\Http\Controllers\TournamentController@listWithGroup')->middleware('api-auth');

Route::prefix('group')->group(function () {
    Route::get('/','App\Http\Controllers\GroupController@list')->middleware('api-auth');
    Route::get('/{id}','App\Http\Controllers\GroupController@getById')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\GroupController@create')->middleware('api-auth');
    Route::put('/{id}','App\Http\Controllers\GroupController@update')->middleware('api-auth');
    Route::delete('/{id}','App\Http\Controllers\GroupController@delete')->middleware('api-auth');
});

Route::prefix('match')->group(function () {
    Route::get('/tournament','App\Http\Controllers\MatchController@list')->middleware('api-auth');
    Route::get('/tournament/{tournament_id}','App\Http\Controllers\MatchController@tournamentMatchsById')->middleware('api-auth');
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
    Route::get('users/{id}','App\Http\Controllers\NotificationController@getNotificationViewers')->middleware('api-auth');
    Route::delete('users/{id}','App\Http\Controllers\NotificationController@deleteNotificationUser')->middleware('api-auth');
});


Route::get('/test', function() {

    return response()->json([
        "message" => "Test OK"
    ]);
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('optimize');

    return response()->json([
        "message" => "Cache cleared successfully"
    ]);
});

Route::post('/command-artisan', function(Request $request) {
    try{
        if(!$request->has('command')){
            return response()->json([
                "message" => "Comando no identificado"
            ]);
        }
        $command = $request->command;

        Artisan::call($command);

        return response()->json([
            "message" => "Comando ejecutado correctamente"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "message" => $e->getMessage()
        ],500);
    }
});

Route::prefix('user-prediction')->group(function () {
    Route::post('/','App\Http\Controllers\UserPredictionController@setPrediction')->middleware('api-auth');
    Route::get('/{tournament_id}/{user_id}','App\Http\Controllers\UserPredictionController@getPredictionByUserId')->middleware('api-auth');

});

Route::prefix('desafio')->group(function () {
    Route::get('/','App\Http\Controllers\DesafioController@list')->middleware('api-auth');
    Route::post('/','App\Http\Controllers\DesafioController@create')->middleware('api-auth');
    Route::put('/estado/{id}','App\Http\Controllers\DesafioController@updateState')->middleware('api-auth');
});
