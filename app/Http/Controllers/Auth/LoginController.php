<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'login' => 'Эти учетные данные не соответствуют нашим записям'
            ]);
        }
        return response()->json(['message' => 'Успешный вход'], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Сгенерировать новый идентификатор для сессии пользователя
        $request->session()->invalidate();

        // Сгенерировать новые значения для CSRF-токена
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Успешный выход'], 200);
    }
}
