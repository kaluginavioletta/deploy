<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Catalog\DessertController;
use App\Http\Controllers\Catalog\DrinkController;
use App\Http\Controllers\Catalog\SushiController;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Orders\OrderController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Storage;

Route::middleware('App\Http\Middleware\Cors')->group(function() {
    Route::get('/main', [MainController::class, 'index']);

    Route::get('/sushi', [SushiController::class, 'index']);
    Route::get('/sushi/{id}', [SushiController::class, 'show']);

    Route::get('/drink', [DrinkController::class, 'index']);
    Route::get('/drink/{id}', [DrinkController::class, 'show']);

    Route::get('/dessert', [DessertController::class, 'index']);
    Route::get('/dessert/{id}', [DessertController::class, 'show']);

    Route::get('/cart', [CartController::class, 'showCart'])->middleware('auth');
    Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->middleware('auth');

    Route::patch('/add-quantity-product/{id}', [CartController::class, 'increaseQuantity'])->middleware('auth');
    Route::patch('/delete-quantity-product/{id}', [CartController::class, 'decreaseQuantity'])->middleware('auth');

    Route::delete('/remove-from-cart/{id}', [CartController::class, 'removeFromCart'])->middleware('auth');

    Route::post('/add-to-favorite/{id}', [FavoriteController::class, 'addToFavorites'])->middleware('auth');
    Route::get('/favorites', [FavoriteController::class, 'showFavorites'])->middleware('auth');
    Route::delete('/remove-from-favorite/{id}', [FavoriteController::class, 'removeFromFavorites'])->middleware('auth');

    Route::get('/orders', [OrderController::class, 'showOrders'])->middleware('auth');
    Route::post('/create-order', [OrderController::class, 'createOrder'])->middleware('auth');
});

Route::get('/images/{filename}', function ($filename) {
    $path = storage_path('app/public/images/' . $filename);
    if (File::exists($path)) {
        return response()->file($path);
    }
    abort(404);
});


Route::middleware(['App\Http\Middleware\Cors', '\App\Http\Middleware\CheckAdminRole'])->group(function() {
    Route::post('/create/sushi', [SushiController::class, 'create']);
    Route::post('/sushi/update/{id}', [SushiController::class, 'update']);
    Route::delete('/sushi/delete/{id}', [SushiController::class, 'destroy']);

    Route::post('/create/drink', [DrinkController::class, 'create']);
    Route::post('/drink/update/{id}', [DrinkController::class, 'update']);
    Route::delete('/drink/delete/{id}', [DrinkController::class, 'destroy']);

    Route::post('/create/dessert', [DessertController::class, 'create']);
    Route::post('/dessert/update/{id}', [DessertController::class, 'update']);
    Route::delete('/dessert/delete/{id}', [DessertController::class, 'destroy']);

    Route::post('/orders/{orderId}/status', [OrderController::class, 'updateStatus']);

    Route::post('/create/drink', [DrinkController::class, 'create']);

    Route::post('/create/dessert', [DessertController::class, 'create']);
});

Route::middleware('App\Http\Middleware\Cors')->group(function() {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');
    Route::post('/authorization', [LoginController::class, 'login'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
});
