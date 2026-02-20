<?php

use App\Http\Controllers\Admin\BancaController;
use App\Http\Controllers\Admin\CargoController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdPostController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\InicioController;
use App\Http\Controllers\Admin\InstituicaoController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\ProgressController as AdminProgressController;
use App\Http\Controllers\Admin\QuestaoController;
use App\Http\Controllers\Admin\SimuladoController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Auth\CadastroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FeedbackTicketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeusSimuladosController;
use App\Http\Controllers\MetricsConsentController;
use App\Http\Controllers\NotificationFeedController;
use App\Http\Controllers\NotificationReadController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimuladoCatalogController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/simulados', [SimuladoCatalogController::class, 'index'])->name('simulados.public');
Route::post('/simulados/{simulado}/iniciar', [SimuladoCatalogController::class, 'start'])->name('simulados.start');
Route::get('/simulados/{simulado}/realizar', [SimuladoCatalogController::class, 'play'])->name('simulados.play');
Route::post('/simulados/{simulado}/realizar', [SimuladoCatalogController::class, 'submit'])->name('simulados.submit');
Route::get('/simulados/{simulado}/resultado', [SimuladoCatalogController::class, 'result'])->name('simulados.result');
Route::get('/simulados/{simulado}/resultado/{tentativa}/questao/{resposta}', [SimuladoCatalogController::class, 'resultQuestion'])
    ->name('simulados.result.question');
Route::post('/questoes/{questao}/responder', [HomeController::class, 'answer'])->name('home.answer');
Route::post('/feedback/tickets', [FeedbackTicketController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('feedback.tickets.store');
Route::post('/lgpd/consentimento-metricas', [MetricsConsentController::class, 'grant'])
    ->middleware('throttle:20,1')
    ->name('metrics.consent.grant');
Route::post('/lgpd/metricas', [MetricsConsentController::class, 'storeMetric'])
    ->middleware('throttle:80,1')
    ->name('metrics.capture');

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
    Route::get('/notificacoes/feed', [NotificationFeedController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('notifications.feed');
    Route::patch('/notificacoes/visualizar/{notificationKey}', [NotificationReadController::class, 'read'])
        ->where('notificationKey', '[A-Za-z0-9\-]+')
        ->name('notifications.read');

    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::post('/perfil/avatar', [ProfileController::class, 'updateAvatar'])->name('perfil.avatar.update');

    Route::get('/area_aluno', [StudentDashboardController::class, 'show'])
        ->middleware('profile:user')
        ->name('area_aluno');

    Route::get('/area_assinante', [StudentDashboardController::class, 'show'])
        ->middleware('profile:user_assinante')
        ->name('area_assinante');

    Route::middleware('profile:user,user_assinante')->group(function () {
        Route::get('/progresso', [ProgressController::class, 'index'])->name('progresso.index');
        Route::get('/progresso/respostas/{questaoResposta}', [ProgressController::class, 'show'])->name('progresso.show');
        Route::get('/meus-simulados', [MeusSimuladosController::class, 'index'])->name('meus-simulados.index');
        Route::get('/dashboard/atividades-pendentes', [StudentDashboardController::class, 'pendingActivities'])
            ->middleware('throttle:60,1')
            ->name('dashboard.pending-activities');
    });

    Route::prefix('adm')->name('adm.')->middleware('profile:adm')->group(function () {
        Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
        Route::get('/inicio/metricas', [InicioController::class, 'metrics'])
            ->middleware('throttle:60,1')
            ->name('inicio.metrics');
        Route::get('/inicio/metricas/detalhes', [InicioController::class, 'details'])
            ->middleware('throttle:60,1')
            ->name('inicio.metrics.details');

        Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::patch('/configuracoes/feed', [ConfiguracaoController::class, 'updateFeedbackFeed'])->name('configuracoes.feed.update');
        Route::patch('/configuracoes/adsense', [ConfiguracaoController::class, 'updateAdsense'])->name('configuracoes.adsense.update');

        Route::get('/anuncios', [AdPostController::class, 'index'])->name('anuncios.index');
        Route::get('/anuncios/adicionar', [AdPostController::class, 'create'])->name('anuncios.create');
        Route::get('/anuncios/verificar-campo', [AdPostController::class, 'checkField'])->name('anuncios.check-field');
        Route::post('/anuncios', [AdPostController::class, 'store'])->name('anuncios.store');
        Route::get('/anuncios/{anuncio}/editar', [AdPostController::class, 'edit'])->name('anuncios.edit');
        Route::put('/anuncios/{anuncio}', [AdPostController::class, 'update'])->name('anuncios.update');
        Route::delete('/anuncios/{anuncio}', [AdPostController::class, 'destroy'])->name('anuncios.destroy');

        Route::get('/bancas', [BancaController::class, 'index'])->name('bancas.index');
        Route::get('/bancas/adicionar', [BancaController::class, 'create'])->name('bancas.create');
        Route::get('/bancas/verificar-nome', [BancaController::class, 'checkName'])->name('bancas.check-name');
        Route::get('/bancas/verificar-campo', [BancaController::class, 'checkField'])->name('bancas.check-field');
        Route::post('/bancas', [BancaController::class, 'store'])->name('bancas.store');
        Route::get('/bancas/{banca}/editar', [BancaController::class, 'edit'])->name('bancas.edit');
        Route::put('/bancas/{banca}', [BancaController::class, 'update'])->name('bancas.update');
        Route::delete('/bancas/{banca}', [BancaController::class, 'destroy'])->name('bancas.destroy');

        Route::get('/simulados', [SimuladoController::class, 'index'])->name('simulados.index');
        Route::get('/simulados/adicionar', [SimuladoController::class, 'create'])->name('simulados.create');
        Route::get('/simulados/verificar-nome', [SimuladoController::class, 'checkName'])->name('simulados.check-name');
        Route::get('/simulados/verificar-campo', [SimuladoController::class, 'checkField'])->name('simulados.check-field');
        Route::post('/simulados', [SimuladoController::class, 'store'])->name('simulados.store');
        Route::get('/simulados/{simulado}/editar', [SimuladoController::class, 'edit'])->name('simulados.edit');
        Route::put('/simulados/{simulado}', [SimuladoController::class, 'update'])->name('simulados.update');
        Route::delete('/simulados/{simulado}', [SimuladoController::class, 'destroy'])->name('simulados.destroy');

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
        Route::get('/progresso', [AdminProgressController::class, 'index'])->name('progresso.index');

        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

        Route::get('/notificacoes/{notification}/abrir', [AdminNotificationController::class, 'open'])->name('notifications.open');
        Route::patch('/notificacoes/{notification}/visualizar', [AdminNotificationController::class, 'read'])->name('notifications.read');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

