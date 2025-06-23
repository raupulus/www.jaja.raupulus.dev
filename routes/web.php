<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*************************************************
 * Autenticación
 ************************************************/
Route::get('/login', static fn () => redirect()->route('filament.panel.auth.login'))->name('login');
//Route::get('/login', fn () => abort(404))->name('login');


/*************************************************
 * Páginas principales
 ************************************************/
Route::get('/', [IndexController::class, 'index'])->name('index');

/*************************************************
 * Páginas dinámicas
 ************************************************/
Route::get('/paginas', [PageController::class, 'index'])->name('page.index');
Route::get('/pagina/{page:slug}', [PageController::class, 'show'])->name('page.show');

/*************************************************
 * Solicitudes
 ************************************************/
Route::post('/suggestion/send', [IndexController::class, 'sendSuggestion'])
    ->middleware('recaptcha:0.7')
    ->name('suggestion.send');

/*************************************************
 * Colaboradores
 ************************************************/
Route::get('/colaboradores', [CollaboratorController::class, 'index'])->name('collaborator.index');
Route::get('/colaborador/{collaborator:nick}', [CollaboratorController::class, 'show'])->name('collaborator.show');
Route::get('/colaborador/{collaborator:nick}/project/{project:slug}', [CollaboratorController::class, 'showProject'])->name('collaborator.project.show');

