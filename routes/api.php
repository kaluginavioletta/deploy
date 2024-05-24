<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Catalog\DessertController;
use App\Http\Controllers\Catalog\DrinkController;
use App\Http\Controllers\Catalog\SushiController;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Orders\OrderController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/main', [MainController::class, 'index']);

Route::middleware('App\Http\Middleware\Cors')->group(function() {
    Route::get('/sushi', [SushiController::class, 'index']);
    Route::get('/sushi/{id}', [SushiController::class, 'show']);

    Route::get('/drink', [DrinkController::class, 'index']);
    Route::get('/drink/{id}', [DrinkController::class, 'show']);

    Route::get('/dessert', [DessertController::class, 'index']);
    Route::get('/dessert/{id}', [DessertController::class, 'show']);

    Route::get('/cart', [CartController::class, 'showCart'])->middleware('auth');
    Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->middleware('auth');
    Route::delete('/remove-from-cart/{id}', [CartController::class, 'removeFromCart'])->middleware('auth');

    Route::post('/add-to-favorite/{id}', [FavoriteController::class, 'addToFavorites'])->middleware('auth');
    Route::get('/favorites', [FavoriteController::class, 'showFavorites'])->middleware('auth');
    Route::delete('/remove-from-favorite/{id}', [FavoriteController::class, 'removeFromFavorites'])->middleware('auth');

    Route::get('/orders', [OrderController::class, 'showOrders'])->middleware('auth');
    Route::post('/create-order', [OrderController::class, 'createOrder'])->middleware('auth');
});

Route::middleware(['App\Http\Middleware\Cors', 'admin'])->group(function() {
    Route::get('/create/sushi', [SushiController::class, 'create'])->middleware('admin');

    Route::delete('/sushi/delete/{id}', [SushiController::class, 'destroy']);
});

Route::prefix('sanctum')->middleware('App\Http\Middleware\Cors')->group(function() {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');
    Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
});
