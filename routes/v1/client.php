<?php

use App\Http\Controllers\V1\Client\GameController;
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

Route::group(['prefix' => 'games', 'as' => 'games', 'middleware' => 'auth:sanctum'], function () {
    Route::post('list', [GameController::class, 'index'])->name('list');
    Route::post('show/{id}', [GameController::class, 'show'])->name('show');
});
