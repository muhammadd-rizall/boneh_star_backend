<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate the user
        if (! auth()->attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = auth()->user();

        // check user aktif
        if (! $user->is_active) {
            auth()->logout();

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // generate sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // load role and permissions
        $user->load('roles.permissions');

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);

    }
}
