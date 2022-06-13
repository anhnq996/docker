<?php

use App\Http\Controllers\V1\Admin\GameController;
use App\Http\Controllers\V1\Admin\PlanController;
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
    Route::post('create', [GameController::class, 'create'])->name('create');
    Route::post('update', [GameController::class, 'update'])->name('update');
    Route::post('show', [GameController::class, 'show'])->name('show');
    Route::post('delete', [GameController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'plans', 'as' => 'plans', 'middleware' => 'auth:sanctum'], function () {
    Route::post('list', [PlanController::class, 'index'])->name('list');
    Route::post('create', [PlanController::class, 'store'])->name('create');
    Route::post('update', [PlanController::class, 'update'])->name('update');
    Route::post('show', [PlanController::class, 'show'])->name('show');
    Route::post('delete', [PlanController::class, 'destroy'])->name('destroy');
    Route::post('select', [PlanController::class, 'select'])->name('select');
});
