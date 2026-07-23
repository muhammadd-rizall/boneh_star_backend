<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! ($user instanceof User)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Layanan ini memerlukan hak akses administrator.',
            ], 403);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda tidak aktif.',
            ], 403);
        }

        if (! $user->roles()->where('is_active', true)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki peran (role) administrator atau staf yang aktif.',
            ], 403);
        }

        return $next($request);
    }
}
