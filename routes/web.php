<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/',[MainController::class, 'home'])->name('home');

Route::get('/login',[MainController::class, 'login'])->name('login');

Route::get('/signup',[MainController::class, 'login'])->name('signup');
