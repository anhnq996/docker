<?php

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

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {

    Route::group([
        'prefix' => 'common.',
        'as' => 'common.',
        'namespace' => 'App\Http\Controllers\V1',
    ], function () {
        require_once __DIR__ . '/v1/common.php';
    });

    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\V1\Admin',
    ], function () {
        require_once __DIR__ . '/v1/admin.php';
    });

    Route::group([
        'prefix' => 'auth',
        'as' => 'auth.',
        'namespace' => 'App\Http\Controllers\V1',
    ], function () {
        require_once __DIR__ . '/v1/auth.php';
    });

    Route::group([
        'prefix' => 'client',
        'as' => 'client.',
        'namespace' => 'App\Http\Controllers\V1\Client',
    ], function () {
        require_once __DIR__ . '/v1/client.php';
    });

    Route::group([
        'prefix' => 'guest',
        'as' => 'guest.',
        'namespace' => 'App\Http\Controllers\V1',
    ], function () {
        require_once __DIR__ . '/v1/guest.php';
    });

    Route::group([
        'prefix' => 'homepage',
        'as' => 'homepage.',
        'namespace' => 'App\Http\Controllers\V1\HomePage',
    ], function () {
        require_once __DIR__ . '/v1/homepage.php';
    });
});
