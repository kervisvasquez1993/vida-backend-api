<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PagoMovilController;
use App\Http\Controllers\PlanController;
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

    // planes
    Route::get('/planes', [PlanController::class, 'index'])->name('index');
    Route::post('/planes', [PlanController::class, 'store'])->name('store');
    Route::get('/planes/{id}', [PlanController::class, 'show'])->name('show');
    Route::put('/planes/{id}', [PlanController::class, 'update'])->name('update');
    Route::delete('/planes/{id}', [PlanController::class, 'destroy'])->name('destroy');

    // clientes
    Route::get('/clientes', [ClientController::class, 'index'])->name('index');
    Route::post('/clientes', [ClientController::class, 'store'])->name('store');
    Route::get('/clientes/{id}', [ClientController::class, 'show'])->name('show');
    Route::put('/clientes/{id}', [ClientController::class, 'update'])->name('update');
    Route::delete('/clientes/{id}', [ClientController::class, 'destroy'])->name('destroy');
    
});
