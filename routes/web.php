<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

/*************************************************
 * Páginas principales
 ************************************************/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/api', [IndexController::class, 'index'])->name('api');
Route::get('/about', [IndexController::class, 'index'])->name('about');

/*************************************************
 * Solicitudes
 ************************************************/
Route::post('/suggestion/send', [IndexController::class, 'sendSuggestion'])->name('suggestion.send');
