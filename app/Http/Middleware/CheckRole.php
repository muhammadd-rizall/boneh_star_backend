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
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }


        // Check if user has one of the required roles
        if(!in_array($user->role->slug, $roles)){
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource.',
                'required_roles' => $roles,
                'your_role' => $user->role->slug,
            ], 403);
        }

        return $next($request);
    }
}
