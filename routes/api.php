<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Catalog\SushiController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;


Route::middleware('App\Http\Middleware\Cors')->group(function() {
    Route::get('/sushi', [SushiController::class, 'index']);
    Route::get('/sushi/{id}', [SushiController::class, 'show']);
    Route::post('/sushi/{id}/add-to-cart', [SushiController::class, 'addToCart'])->middleware('auth');
});

Route::middleware(['App\Http\Middleware\Cors', 'admin'])->group(function() {
    Route::delete('/sushi/delete/{id}', [SushiController::class, 'destroy']);
});

Route::prefix('sanctum')->middleware('App\Http\Middleware\Cors')->group(function() {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');
    Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
});

//Route::view('/', 'home')->name('/');
//Route::get('profile', fn() => 'profile')->middleware('auth')->name('profile');
//Route::get('/sushi', [SushiController::class, 'index']);
//Route::post('/admin/add/sushi', [SushiController::class, 'store']);
    // на delete настроить middleware для админа
//    Route::delete('/sushi/delete/{id}', [SushiController::class, 'destroy'])->middleware('admin');
//    Route::get('/sushi/{id}', [SushiController::class, 'show'])->middleware('auth');
//});

//Route::post('/sushi/{id}/add-to-cart', [SushiController::class, 'addToCart'])->middleware('auth:sanctum');
//
//Route::prefix('sanctum')->group(function() {
//    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');
//    Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
//    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
//    // Отправка методом post, поскольку выполняем выход через CSRF-токен
//    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
//});
