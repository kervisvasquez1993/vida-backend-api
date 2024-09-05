<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagoMovilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    // modulo para registrar bancos
    Route::post('/pago-movil', [PagoMovilController::class, 'store'])->name('store');
});
