<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();


        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Anda tidak valid atau telah berakhir.',
            ], 401);
        }


        // Check if user has one of the required roles
        $hasAnyRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasAnyRole = true;
                break;
            }
        }

        if (!$hasAnyRole) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses (peran) untuk mengakses layanan ini.',
                'required_roles' => $roles,
                'your_roles' => $user->roles->pluck('slug'),
            ], 403);
        }

        return $next($request);
    }
}
