<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\LogoutController;

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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ResetPasswordController::class, 'ForgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'ResetPasswordTokenCheck'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword'])->name('password.update');

Route::middleware('auth:api')->group(function () {
    Route::get('/login', [LoginController::class, 'login']);
    Route::post('/change-password', [ChangePasswordController::class, 'ChangePassword']);
    Route::get('/logout', [LogoutController::class, 'logout']);
    Route::get('/logout/all', [LogoutController::class, 'logout_all']);

    
});