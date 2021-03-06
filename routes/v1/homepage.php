<?php

use App\Http\Controllers\V1\Homepage\PlanController;
use App\Http\Controllers\V1\Homepage\ContactController;
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

Route::post('plans/list', [PlanController::class, 'index'])->name('list');
Route::post('contacts/create', [ContactController::class, 'create'])->name('list');
