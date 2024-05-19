<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('login', $request->login)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Эти учетные данные не соответствуют нашим записям'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->api_token = $token;
        $user->save();

        // Сохранение токена в сессии
        Session::put('auth_token', $token);

        return response()->json(['message' => 'Успешный вход', 'token' => $token], 200);
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
