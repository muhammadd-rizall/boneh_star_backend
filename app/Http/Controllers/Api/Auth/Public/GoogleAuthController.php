<?php

namespace App\Http\Controllers\Api\Auth\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends BaseController
{
    /**
     * Redirect customer to Google OAuth provider using Socialite.
     */
    public function redirectToGoogle(Request $request): mixed
    {
        $driver = Socialite::driver('google')->stateless();
        if (app()->environment('local')) {
            $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        }

        if ($request->wantsJson() || $request->query('mode') === 'json') {
            $url = $driver->redirect()->getTargetUrl();
            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        }

        return $driver->redirect();
    }

    /**
     * Handle callback response from Google OAuth via Socialite.
     */
    public function handleGoogleCallback(): mixed
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        try {
            $driver = Socialite::driver('google')->stateless();
            if (app()->environment('local')) {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $googleUser = $driver->user();

            $email = strtolower(trim($googleUser->getEmail()));
            $name = trim($googleUser->getName() ?: 'Pelanggan Google');
            $googleId = $googleUser->getId();
            $profilePicture = $googleUser->getAvatar();

            // Find customer by google_id or email
            $customer = Customer::where('email', $email)
                ->orWhere(function ($query) use ($googleId) {
                    if ($googleId) {
                        $query->where('google_id', $googleId);
                    }
                })->first();

            if ($customer) {
                // Update google_id or profile picture if missing
                $updates = [];
                if ($googleId && !$customer->google_id) {
                    $updates['google_id'] = $googleId;
                }
                if ($profilePicture && !$customer->profile_picture) {
                    $updates['profile_picture'] = $profilePicture;
                }
                if (!empty($updates)) {
                    $customer->update($updates);
                }
            } else {
                // Create new customer account
                $customer = Customer::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => bcrypt(Str::random(24)),
                    'profile_picture' => $profilePicture,
                    'is_active' => true,
                ]);
            }

            // Check active status
            if (!$customer->is_active) {
                return redirect($frontendUrl . '/auth/login?error=' . urlencode('Akun Anda dinonaktifkan. Silakan hubungi admin.'));
            }

            // Issue Sanctum Token
            $token = $customer->createToken('auth_token')->plainTextToken;

            // Redirect to frontend callback route with token & user payload
            return redirect($frontendUrl . '/auth/callback?token=' . urlencode($token) . '&user=' . urlencode(json_encode(new CustomerResource($customer))));
        } catch (\Exception $e) {
            return redirect($frontendUrl . '/auth/login?error=' . urlencode('Gagal memproses autentikasi Google: ' . $e->getMessage()));
        }
    }

    /**
     * Handle Direct Google API POST login for Customers.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'name' => ['required', 'string'],
            'google_id' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'string'],
        ]);

        $email = strtolower(trim($request->email));
        $name = trim($request->name);
        $googleId = $request->google_id;
        $profilePicture = $request->profile_picture;

        $customer = Customer::where('email', $email)
            ->orWhere(function ($query) use ($googleId) {
                if ($googleId) {
                    $query->where('google_id', $googleId);
                }
            })->first();

        if ($customer) {
            $updates = [];
            if ($googleId && !$customer->google_id) {
                $updates['google_id'] = $googleId;
            }
            if ($profilePicture && !$customer->profile_picture) {
                $updates['profile_picture'] = $profilePicture;
            }
            if (!empty($updates)) {
                $customer->update($updates);
            }
        } else {
            $customer = Customer::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'password' => bcrypt(Str::random(24)),
                'profile_picture' => $profilePicture,
                'is_active' => true,
            ]);
        }

        if (!$customer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
            ], 403);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil masuk dengan Google',
            'data' => [
                'user' => new CustomerResource($customer),
                'token' => $token,
            ],
        ], 200);
    }
}
