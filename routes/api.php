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
