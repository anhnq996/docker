<?php

use App\Http\Controllers\V1\AuthController;
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

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/get', [AuthController::class, 'getUser'])->name('getUser');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change_password');
    Route::post('/update-user', [AuthController::class, 'updateUser'])->name('update_user');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
