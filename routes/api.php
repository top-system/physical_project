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
Route::any('v1/get_leagues',\App\Http\Controllers\FootballController::class . "@getLeagues");
Route::any('v1/get_events',\App\Http\Controllers\FootballController::class . "@getEvents");
Route::any('v1/get_events/{id}',\App\Http\Controllers\FootballController::class . "@getEvent");
Route::any('v1/get_posts',\App\Http\Controllers\PostController::class . "@getPosts");
Route::any('v1/get_categories',\App\Http\Controllers\CategoryController::class . "@getCategories");
Route::any('v1/get_posts/{id}',\App\Http\Controllers\PostController::class . "@getPost");
Route::any('v1/get_h2h',\App\Http\Controllers\FootballController::class . "@getH2H");
Route::any('v1/get_lineups/{match_id}',\App\Http\Controllers\FootballController::class . "@getLineups");
Route::any('v1/get_live_odds_commnets',\App\Http\Controllers\FootballController::class . "@getLiveOddsCommnets");
Route::any('v1/get_chatrecode/{room_id}',\App\Http\Controllers\ChatRoomController::class . "@getChatRecode");
Route::any('v1/get_index/{match_id}',\App\Http\Controllers\FootballController::class . "@getIndex");
Route::any('v1/datastore/get_race',\App\Http\Controllers\DataStoreController::class . "@getRace");
Route::any('v1/datastore/get_score',\App\Http\Controllers\DataStoreController::class . "@getScore");
Route::any('v1/datastore/get_scorers',\App\Http\Controllers\DataStoreController::class . "@getScorers");
