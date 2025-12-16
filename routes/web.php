<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

//rotas que necessitam de autenticação
Route::middleware([('auth')])->group(function() {

    Route::get('/',[MainController::class, 'home'])->name('home');
    Route::get('/profile',[MainController::class, 'profile'])->name('profile');



});

//rotas que usuários não logados poderão acessar mas usuários logados não
Route::middleware([('guest')])->group(function() {

    Route::get('/login',[MainController::class, 'login'])->name('login');

    Route::get('/signup',[MainController::class, 'signup'])->name('signup');

});
