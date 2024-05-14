<?php

use App\Http\Controllers\Catalog\SushiController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::view('/', 'home')->name('/');
//Route::get('profile', fn() => 'profile')->middleware('auth')->name('profile');

// Нужно ли в качестве route для get прописывать, отображение страницы
//Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
//Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

//Route::get('/sushi', [SushiController::class, 'index']);
Route::prefix('sanctum')->group(function() {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');
    Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
    // Отправка методом post, поскольку выполняем выход через CSRF-токен
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
});

Route::get('/sushi', [SushiController::class, 'index'])->middleware('guest');

// Нужно ли в качестве route для get прописывать, отображение страницы
//Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
//Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
