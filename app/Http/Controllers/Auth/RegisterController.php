<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return User::all();
    }

    // Обработка данных, которые приходят с сервера
    public function store(Request $request)
    {
        try {
            $request->validate([
                'surname' => 'required|string',
                'name' => 'required|string',
                'patronymic' => 'required|string',
                'tel' => [
                    'required',
                    'numeric',
                    'digits:11', // Используем 'digits:11' вместо regex для 11 цифр
                    'min:11', // Добавляем 'min:11' для минимальной длины
                ],
                'email' => 'nullable|string|email|max:255|unique:users', // Валидация email
                'login' => 'required|unique:users',
                'password' => 'required|confirmed'
            ], [
                'tel.digits' => 'Номер телефона должен состоять из 11 цифр.',
                'tel.min' => 'Номер телефона должен состоять не менее чем из 11 цифр.',
                'login.unique' => 'Этот логин уже занят.',
            ]);

            $user = User::create([
                'surname' => $request->surname,
                'name' => $request->name,
                'patronymic' => $request->patronymic,
                'tel' => $request->tel,
                'email' => $request->email,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'id_role' => Role::where('name_role', 'user')->first()->id_role,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['user' => $user, 'access_token' => $token], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}
