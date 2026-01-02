<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

//user-related routes
Route::middleware('auth')->group(function() {

    Route::get('/profile',[UserController::class, 'profile'])->name('profile');
});


    
//products
Route::middleware('auth')->prefix('products')->group(function() {

    Route::get('create', [ProductController::class, 'create'])->name('products.create');
    Route::post('store', [ProductController::class, 'store'])->name('products.store');

    Route::get('show/{id}' , [ProductController::class, 'show' ])->name('products.show');

});

//cart routes
Route::middleware('auth')->prefix('cart')->group(function() {
    
    Route::get('/', [OrderController::class, 'index'])->name('cart.index');

    Route::post('/add/{product}', [OrderController::class, 'add'])->name('cart.add');

    Route::patch('/update/{item}', [OrderController::class, 'update'])->name('cart.update');

    Route::post('/delete/{item}', [OrderController::class, 'delete'])->name('cart.delete');

});

// dashboard
Route::get('/',[DashboardController::class, 'home'])->name('home');

//rotas que usuários não logados poderão acessar mas usuários logados não
Route::middleware('guest')->group(function() {

    Route::view('/login', 'auth.login')->name('login');

    Route::view('/signup', 'auth.signup')->name('signup');

});
