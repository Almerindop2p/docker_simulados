<?php

use App\Http\Controllers\Admin\BancaController;
use App\Http\Controllers\Admin\CargoController;
use App\Http\Controllers\Admin\InstituicaoController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\QuestaoController;
use App\Http\Controllers\Auth\CadastroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FeedbackTicketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/questoes/{questao}/responder', [HomeController::class, 'answer'])->name('home.answer');
Route::post('/feedback/tickets', [FeedbackTicketController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('feedback.tickets.store');

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
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::post('/perfil/avatar', [ProfileController::class, 'updateAvatar'])->name('perfil.avatar.update');

    Route::get('/area_aluno', function () {
        return view('area_aluno');
    })->middleware('profile:user')->name('area_aluno');

    Route::get('/area_assinante', function () {
        return view('area_aluno');
    })->middleware('profile:user_assinante')->name('area_assinante');

    Route::prefix('adm')->name('adm.')->middleware('profile:adm')->group(function () {
        Route::get('/bancas', [BancaController::class, 'index'])->name('bancas.index');
        Route::get('/bancas/adicionar', [BancaController::class, 'create'])->name('bancas.create');
        Route::get('/bancas/verificar-nome', [BancaController::class, 'checkName'])->name('bancas.check-name');
        Route::get('/bancas/verificar-campo', [BancaController::class, 'checkField'])->name('bancas.check-field');
        Route::post('/bancas', [BancaController::class, 'store'])->name('bancas.store');
        Route::get('/bancas/{banca}/editar', [BancaController::class, 'edit'])->name('bancas.edit');
        Route::put('/bancas/{banca}', [BancaController::class, 'update'])->name('bancas.update');
        Route::delete('/bancas/{banca}', [BancaController::class, 'destroy'])->name('bancas.destroy');

        Route::get('/instituicoes', [InstituicaoController::class, 'index'])->name('instituicoes.index');
        Route::get('/instituicoes/adicionar', [InstituicaoController::class, 'create'])->name('instituicoes.create');
        Route::get('/instituicoes/verificar-nome', [InstituicaoController::class, 'checkName'])->name('instituicoes.check-name');
        Route::get('/instituicoes/verificar-campo', [InstituicaoController::class, 'checkField'])->name('instituicoes.check-field');
        Route::post('/instituicoes', [InstituicaoController::class, 'store'])->name('instituicoes.store');
        Route::get('/instituicoes/{instituicao}/editar', [InstituicaoController::class, 'edit'])->name('instituicoes.edit');
        Route::put('/instituicoes/{instituicao}', [InstituicaoController::class, 'update'])->name('instituicoes.update');
        Route::delete('/instituicoes/{instituicao}', [InstituicaoController::class, 'destroy'])->name('instituicoes.destroy');

        Route::get('/materias', [MateriaController::class, 'index'])->name('materias.index');
        Route::get('/materias/adicionar', [MateriaController::class, 'create'])->name('materias.create');
        Route::get('/materias/verificar-campo', [MateriaController::class, 'checkField'])->name('materias.check-field');
        Route::post('/materias', [MateriaController::class, 'store'])->name('materias.store');
        Route::get('/materias/editar/{materia}', [MateriaController::class, 'edit'])->name('materias.edit');
        Route::put('/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
        Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');

        Route::get('/cargos', [CargoController::class, 'index'])->name('cargos.index');
        Route::get('/cargos/adicionar', [CargoController::class, 'create'])->name('cargos.create');
        Route::get('/cargos/verificar-campo', [CargoController::class, 'checkField'])->name('cargos.check-field');
        Route::post('/cargos', [CargoController::class, 'store'])->name('cargos.store');
        Route::get('/cargos/editar/{cargo}', [CargoController::class, 'edit'])->name('cargos.edit');
        Route::put('/cargos/{cargo}', [CargoController::class, 'update'])->name('cargos.update');
        Route::delete('/cargos/{cargo}', [CargoController::class, 'destroy'])->name('cargos.destroy');

        Route::get('/questoes', [QuestaoController::class, 'index'])->name('questoes.index');
        Route::get('/questoes/adicionar', [QuestaoController::class, 'create'])->name('questoes.create');
        Route::post('/questoes', [QuestaoController::class, 'store'])->name('questoes.store');
        Route::get('/questoes/editar/{questao}', [QuestaoController::class, 'edit'])->name('questoes.edit');
        Route::put('/questoes/{questao}', [QuestaoController::class, 'update'])->name('questoes.update');
        Route::delete('/questoes/{questao}', [QuestaoController::class, 'destroy'])->name('questoes.destroy');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

