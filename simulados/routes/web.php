<?php

use App\Http\Controllers\Auth\CadastroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/cadastro', [CadastroController::class, 'create'])->name('cadastro.create');
    Route::post('/cadastro', [CadastroController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('cadastro.store');
});
