<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');;


//Test primeras pruebas mientras entendía como loguearse
/*
Route::get('/login', function () {
    $user = \App\Models\User::find(1);

    auth()->login($user);

    return redirect('/admin');
})->name('login');
*/
