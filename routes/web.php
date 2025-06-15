<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

//Route::get('/login', fn () => redirect()->route('filament.admin.auth.login'))->name('login');
Route::get('/login', fn () => abort(404))->name('login');


/*************************************************
 * Páginas principales
 ************************************************/
Route::get('/', [IndexController::class, 'index'])->name('index');


Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');


Route::get('/page/api', [IndexController::class, 'api'])->name('api');
Route::get('/page/about', [IndexController::class, 'about'])->name('about');
Route::get('/page/normas', [IndexController::class, 'normas'])->name('normas');
Route::get('/page/politica-de-privacidad', [IndexController::class, 'privacity'])->name('privacity');
Route::get('/page/politica-de-cookies', [IndexController::class, 'cookies'])->name('cookies');
Route::get('/page/condiciones-de-uso', [IndexController::class, 'conditions'])->name('conditions');
Route::get('/page/agradecimientos', [IndexController::class, 'agradecimientos'])->name('agradecimientos');



/*************************************************
 * Solicitudes
 ************************************************/
Route::post('/suggestion/send', [IndexController::class, 'sendSuggestion'])->name('suggestion.send');

