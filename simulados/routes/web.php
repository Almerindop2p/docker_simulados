<?php

use App\Http\Controllers\Auth\CadastroController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');

    Route::get('/cadastro', [CadastroController::class, 'create'])->name('cadastro.create');
    Route::post('/cadastro', [CadastroController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('cadastro.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
