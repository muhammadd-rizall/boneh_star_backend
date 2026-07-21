<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Anda tidak valid atau telah berakhir.',
            ], 401);
        }

        // Split multiple permissions if provided (e.g. 'users.view,users.create')
        $permissionArray = explode(',', $permissions);

        $hasPermission = false;
        foreach ($permissionArray as $permission) {
            if ($user->hasPermissionTo(trim($permission))) {
                $hasPermission = true;
                break;
            }
        }

        if (! $hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses (izin khusus) untuk mengakses layanan ini.',
            ], 403);
        }

        return $next($request);
    }
}
