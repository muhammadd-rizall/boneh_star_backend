<?php

namespace App\Http\Controllers\Api\Auth\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Admin\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate the customer
        if (! auth()->guard('customer')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $customer = auth()->guard('customer')->user();

        // check customer aktif
        if (! $customer->is_active) {
            auth()->guard('customer')->logout();

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // generate sanctum token
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => new CustomerResource($customer),
                'token' => $token,
            ],
        ], 200);
    }
}
