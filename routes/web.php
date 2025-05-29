<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

//Route::get('/login', fn () => redirect()->route('filament.admin.auth.login'))->name('login');
Route::get('/login', fn () => abort(404))->name('login');


/*************************************************
 * Páginas principales
 ************************************************/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/page/api', [IndexController::class, 'index'])->name('api');
Route::get('/page/about', [IndexController::class, 'index'])->name('about');

/*************************************************
 * Solicitudes
 ************************************************/
Route::post('/suggestion/send', [IndexController::class, 'sendSuggestion'])->name('suggestion.send');

