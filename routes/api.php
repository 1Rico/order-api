<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VoucherController;
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

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::group(['middleware' => ['auth:sanctum']], static function () {
    Route::apiResource('vouchers', VoucherController::class);
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
});
