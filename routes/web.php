<?php

declare(strict_types=1);

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandaReprimidaController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\EspecialidadeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UbsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', LandingPageController::class)->name('landing');

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (Operadores e Administradores)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard Operacional
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de UBS
    Route::resource('ubs', UbsController::class);

    // Módulo de Pacientes (com busca reativa)
    Route::resource('pacientes', PacienteController::class);

    // Módulo de Agenda
    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::post('agenda/{agendamento}/chegada', [AgendaController::class, 'registrarChegada'])->name('agenda.chegada');
    Route::patch('agenda/{agendamento}/status', [AgendaController::class, 'atualizarStatus'])->name('agenda.status');
    Route::delete('agenda/{agendamento}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

    // Demanda Reprimida (Fila de Espera)
    Route::get('demanda-reprimida', [DemandaReprimidaController::class, 'index'])->name('demanda-reprimida.index');
    Route::get('demanda-reprimida/create', [DemandaReprimidaController::class, 'create'])->name('demanda-reprimida.create');
    Route::post('demanda-reprimida', [DemandaReprimidaController::class, 'store'])->name('demanda-reprimida.store');
    Route::post('demanda-reprimida/{demanda}/promover', [DemandaReprimidaController::class, 'promover'])->name('demanda-reprimida.promover');
    Route::delete('demanda-reprimida/{demanda}', [DemandaReprimidaController::class, 'destroy'])->name('demanda-reprimida.destroy');

    // Relatórios Gerenciais
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [RelatorioController::class, 'index'])->name('index');
        Route::get('/absenteismo', [RelatorioController::class, 'absenteismo'])->name('absenteismo');
        Route::get('/producao', [RelatorioController::class, 'producao'])->name('producao');
        Route::get('/demanda-reprimida', [RelatorioController::class, 'demandaReprimida'])->name('demanda-reprimida');
    });

    // Especialidades e Dentistas (Visualização para Operadores)
    Route::get('especialidades', [EspecialidadeController::class, 'index'])->name('especialidades.index');
    Route::get('dentistas', [DentistaController::class, 'index'])->name('dentistas.index');

    /*
    |--------------------------------------------------------------------------
    | Rotas Exclusivas do Administrador (Protegidas pelo Middleware 'admin')
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        // Gestão de Especialidades (CRUD Completo)
        Route::get('especialidades/create', [EspecialidadeController::class, 'create'])->name('especialidades.create');
        Route::post('especialidades', [EspecialidadeController::class, 'store'])->name('especialidades.store');
        Route::get('especialidades/{especialidade}/edit', [EspecialidadeController::class, 'edit'])->name('especialidades.edit');
        Route::put('especialidades/{especialidade}', [EspecialidadeController::class, 'update'])->name('especialidades.update');
        Route::delete('especialidades/{especialidade}', [EspecialidadeController::class, 'destroy'])->name('especialidades.destroy');

        // Gestão de Dentistas e Grades Operacionais (CRUD Completo)
        Route::get('dentistas/create', [DentistaController::class, 'create'])->name('dentistas.create');
        Route::post('dentistas', [DentistaController::class, 'store'])->name('dentistas.store');
        Route::get('dentistas/{dentista}/edit', [DentistaController::class, 'edit'])->name('dentistas.edit');
        Route::put('dentistas/{dentista}', [DentistaController::class, 'update'])->name('dentistas.update');
        Route::delete('dentistas/{dentista}', [DentistaController::class, 'destroy'])->name('dentistas.destroy');

        // Gestão de Usuários do Sistema
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Trilha de Auditoria (RF24)
        Route::get('auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');
        Route::get('auditorias/{auditoria}', [AuditoriaController::class, 'show'])->name('auditorias.show');
    });

    // Detalhes do Dentista (acessível para operadores e administradores)
    Route::get('dentistas/{dentista}', [DentistaController::class, 'show'])->name('dentistas.show');
});

require __DIR__.'/auth.php';
