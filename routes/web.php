<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Spatie\Tags\Tag;


// ================= USER =================
Route::middleware('auth')->group(function () {

    Route::get('/profile/{user}', [UserController::class, 'profile'])->name('profile');

    Route::patch('/update/{user}', [UserController::class, 'update'])->name('user.update');

    Route::patch('/pfpupdate/{user}', [UserController::class, 'pfpupdate'])->name('user.pfp.update');

    Route::get('/my-Products', [UserController::class, 'userProducts'])->name('user.products');
});

// ================= PRODUCTS =================
Route::middleware('auth')->prefix('products')->group(function () {

    Route::get('create', [ProductController::class, 'create'])->name('products.create');

    Route::post('store', [ProductController::class, 'store'])->name('products.store');

    Route::get('show/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::patch('update/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::get('edit/{product}', [ProductController::class, 'edit'])->name('products.edit');

    Route::delete('delete/{product}', [ProductController::class, 'delete'])->name('products.delete');
});

// ================= CART =================
Route::middleware('auth')->prefix('cart')->group(function () {

    Route::get('/', [OrderController::class, 'index'])->name('cart.index');

    Route::post('/add/{product}', [OrderController::class, 'add'])->name('cart.add');

    Route::patch('/update/{item}', [OrderController::class, 'update'])->name('cart.update');

    Route::delete('/delete/{item}', [OrderController::class, 'delete'])->name('cart.delete');
});

// ================= CHECKOUT =================
Route::middleware('auth')->group(function () {

    Route::post('/checkout/{order}', [CheckoutController::class, 'checkout'])->name('checkout');

    Route::post('/checkout/{order}/complete', [CheckoutController::class, 'confirmDelivery'])->name('checkout.complete');

    Route::post('/ratings/{orderItem}', [RatingController::class, 'store'])->name('ratings.store');

    Route::post('/withdraw', [CheckoutController::class, 'withdraw'])->name('withdraw');

    Route::get('/my-Orders', [OrderController::class, 'myOrders'])->name('user.orders');
    
    // ============= Stripe-related ================

    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/checkout/cancel/{order}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

});

// ================= Stripe ====================

Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);

Route::post('/orders/{order}/checkout', [StripeController::class, 'checkout'])->name('orders.checkout')->middleware('auth');


// ================= DASHBOARD =================
Route::get('/', [DashboardController::class, 'home'])->name('home');

// ================= TAGS (SPATIE) =================
Route::get('/tags/{tag:slug}', function (Tag $tag) {

    $products = Product::withAnyTags([$tag->name])
        ->latest()
        ->paginate(20);

    return view('tags.show', compact('tag', 'products'));

})->name('tags.show');

// ================= GUEST =================
Route::middleware('guest')->group(function () {

    Route::view('/login', 'auth.login')->name('login');

    Route::view('/signup', 'auth.signup')->name('signup');
});
