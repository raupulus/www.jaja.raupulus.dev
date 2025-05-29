<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\v1;

## Ruta para autenticarse y obtener un token.
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('api.login');

## Devolver un contenido aleatorio de entre todos (Público, 1 cada 5 segundos)
Route::get('/random/get/{quantity?}', [v1::class, 'random'])
    ->where('quantity', '[1-5]')
    ->name('api.get.random');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return auth()->user()->append('urlImage');
    });

    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);



    ## Listado de todos los tipos
    Route::post('/types/list', [v1::class, 'typesList'])->name('api.types.list');

    ## Contenido random en base a un tipo
    Route::post('/type/{type}/get/random', [v1::class, 'getContentRandomFromType'])->name('api.type.get.random');

    ## Listado de todas las categorías
    Route::post('/categories/list', [v1::class, 'categoriesList'])->name('api.categories.list');

    ## Contenido en base a una categoría y un tipo
    Route::post('/type/{type}/category/{category}/get/content/random', [v1::class, 'getContentRandomFromCategory'])
        ->name('api.type.category.get.content.random');



    // TODO: Replantear si esto es realmente interesante, quizás debería paginarlo pero devolver todos de golpe es una locura si crece
    ## Listado de todos los grupos en base a una categoría

    ## Contenido en base a un grupo
});
