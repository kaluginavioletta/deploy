<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = Role::find($user->id_role);

            if ($role && $role->name_role === 'admin') {
                return $next($request);
            } else {
                return response()->json(['error' => 'Вы не администратор'], 403);
            }
        }

        return response()->json(['error' => 'Вы не авторизованы'], 401);
    }
}
