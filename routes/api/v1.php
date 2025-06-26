<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\v1 as ApiController;
use \App\Http\Controllers\Api\AuthController;

## Ruta para autenticarse y obtener un token.
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

## Devolver un contenido aleatorio de entre todos (Público, 1 cada 5 segundos)
Route::get('/random', [ApiController::class, 'random'])
    ->name('api.get.random');

Route::middleware('auth:sanctum')->group(function () {
    ## Cerrar sesión y caducar token.
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    ## Datos del usuario autenticado.
    Route::get('/auth/user', [AuthController::class, 'user'])->name('api.auth.user');

    ## Tipos de contenido.
    Route::get('/types', [ApiController::class, 'typesIndex'])->name('api.types.index');

    ## Grupos de contenido.
    Route::get('/groups', [ApiController::class, 'groupsIndex'])->name('api.groups.index');

    ## Categorías de contenido.
    Route::get('/categories', [ApiController::class, 'categoriesIndex'])->name('api.categories.index');

    ## Contenido random en base a un tipo.
    Route::get('/type/{type:slug}/content/random', [ApiController::class, 'getContentRandomFromType'])
        ->name('api.types.content.random');

    ## Contenido mediante una categoría y un tipo.
    Route::get('/type/{type:slug}/category/{categorySlug}/content/random', [ApiController::class, 'getContentRandomFromCategory'])
        ->name('api.types.categories.content.random');

    ## Contenido en base a un grupo
    Route::get('/group/{group:slug}/content/random', [ApiController::class, 'getContentRandomFromGroup'])
        ->name('api.groups.content.random');
});
