<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1Controller as ApiController;
use \App\Http\Controllers\Api\AuthController;

## Ruta para autenticarse y obtener un token.
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle.real.ip:3,1')
    ->name('api.auth.login');

## Devolver un contenido aleatorio de entre todos
Route::get('/random', [ApiController::class, 'random'])
    ->middleware('throttle.real.ip:10,1')
    ->name('api.get.random');

## Devolver un contenido aleatorio de entre todos
Route::get('/random/{type:slug}', [ApiController::class, 'randomFromType'])
    ->middleware('throttle.real.ip:10,1')
    ->name('api.get.random.from.type');

Route::middleware('auth:sanctum')->group(function () {
    ## Cerrar sesión y caducar token.
    Route::post('/auth/logout', [AuthController::class, 'logout'])
        ->middleware('throttle.real.ip:3,1')
        ->name('api.auth.logout');

    ## Datos del usuario autenticado.
    Route::get('/auth/user', [AuthController::class, 'user'])
        ->middleware('throttle.real.ip:5,1')
        ->name('api.auth.user');

    ## Enviar sugerencia.
    Route::post('/suggestion/send', [ApiController::class, 'sendSuggestion'])
        ->middleware('throttle.real.ip:10,1')
        ->name('api.suggestion.send');

    ## Enviar Reporte
    Route::post('/report/send', [ApiController::class, 'sendReport'])
        ->middleware('throttle.real.ip:10,1')
        ->name('api.report.send');

    ## Tipos de contenido.
    Route::get('/types', [ApiController::class, 'typesIndex'])
        ->middleware('throttle.real.ip:20,1')
        ->name('api.types.index');

    ## Grupos de contenido.
    Route::get('/groups', [ApiController::class, 'groupsIndex'])
        ->middleware('throttle.real.ip:20,1')
        ->name('api.groups.index');

    ## Categorías de contenido.
    Route::get('/categories', [ApiController::class, 'categoriesIndex'])
        ->middleware('throttle.real.ip:20,1')
        ->name('api.categories.index');

    ## Contenido random en base a un tipo.
    Route::get('/type/{type:slug}/content/random', [ApiController::class, 'getContentRandomFromType'])
        ->middleware('throttle.real.ip:50,1')
        ->name('api.types.content.random');

    ## Contenido mediante una categoría y un tipo.
    Route::get('/type/{type:slug}/category/{categorySlug}/content/random', [ApiController::class, 'getContentRandomFromCategory'])
        ->middleware('throttle.real.ip:50,1')
        ->name('api.types.categories.content.random');

    ## Contenido en base a un grupo
    Route::get('/group/{group:slug}/content/random', [ApiController::class, 'getContentRandomFromGroup'])
        ->middleware('throttle.real.ip:50,1')
        ->name('api.groups.content.random');
});
