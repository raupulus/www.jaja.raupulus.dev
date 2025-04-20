<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    $user = \App\Models\User::find(1);

    auth()->login($user);

    return redirect('/admin');
})->name('login');
