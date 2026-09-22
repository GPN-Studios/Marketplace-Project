<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ================= USER =================
Route::middleware('auth')->group(function () {

    Route::get('/profile/{user}', [UserController::class, 'profile'])->name('profile');

    Route::get('/my-Products', [UserController::class, 'userProducts'])->name('user.products');

    Route::middleware('throttle:profile')->group(function () {
        Route::patch('/update/{user}', [UserController::class, 'update'])->name('user.update');

        Route::patch('/pfpupdate/{user}', [UserController::class, 'pfpupdate'])->name('user.pfp.update');
    });
});

// ================= PRODUCTS =================
Route::middleware('auth')->prefix('products')->group(function () {

    Route::get('create', [ProductController::class, 'create'])->name('products.create');
    Route::get('show/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('edit/{product}', [ProductController::class, 'edit'])->name('products.edit');

    Route::middleware(['verified', 'throttle:listings'])->group(function () {
        Route::post('store', [ProductController::class, 'store'])->name('products.store');
        Route::patch('update/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('delete/{product}', [ProductController::class, 'delete'])->name('products.delete');
    });
});

// ================= CART =================
Route::middleware('auth')->prefix('cart')->group(function () {

    Route::get('/', [OrderController::class, 'index'])->name('cart.index');

    Route::middleware('throttle:cart')->group(function () {
        Route::post('/add/{product}', [OrderController::class, 'add'])->name('cart.add');

        Route::patch('/update/{item}', [OrderController::class, 'update'])->name('cart.update');

        Route::delete('/delete/{item}', [OrderController::class, 'delete'])->name('cart.delete');
    });
});

// ================= CHECKOUT =================
Route::middleware('auth')->group(function () {

    Route::get('/my-Orders', [OrderController::class, 'myOrders'])->name('user.orders');

    Route::middleware('throttle:checkout')->group(function () {
        Route::post('/checkout/{order}', [CheckoutController::class, 'checkout'])->name('checkout');

        Route::post('/checkout/{order}/complete', [CheckoutController::class, 'confirmDelivery'])->name('checkout.complete');

        Route::get('/checkout/cancel/{order}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    });

    Route::post('/ratings/{orderItem}', [RatingController::class, 'store'])
        ->name('ratings.store')
        ->middleware('throttle:ratings');

    Route::post('/withdraw', [CheckoutController::class, 'withdraw'])
        ->name('withdraw')
        ->middleware(['verified', 'throttle:withdraw']);

    // ============= Stripe-related ================

    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

});

// ================= Stripe ====================

// Sem rate limit por IP: as notificações vêm da infraestrutura do Stripe (não
// do navegador do usuário) e já são autenticadas pela assinatura HMAC — um
// limite por IP aqui só arriscaria descartar entregas/retentativas legítimas.
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);

Route::post('/orders/{order}/checkout', [StripeController::class, 'checkout'])
    ->name('orders.checkout')
    ->middleware(['auth', 'verified', 'throttle:checkout']);

// ================= DASHBOARD =================
Route::get('/', [DashboardController::class, 'home'])->name('home');

// ================= TAGS (SPATIE) =================
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');

// ================= SEARCH =================
Route::get('/search', [SearchController::class, 'index'])->name('search')->middleware('throttle:search');

// ================= PÁGINAS INSTITUCIONAIS =================
Route::get('/sobre', [PageController::class, 'about'])->name('about');
Route::get('/projeto', [PageController::class, 'project'])->name('project');

// ================= GUEST =================
Route::middleware('guest')->group(function () {

    Route::view('/login', 'auth.login')->name('login');

    Route::view('/signup', 'auth.signup')->name('signup');
});
