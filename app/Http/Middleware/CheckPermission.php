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

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $hasPermission = $user->role->permissions()->whereIn('slug', explode(',', $permissions))->exists();

        if(!$hasPermission){
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
