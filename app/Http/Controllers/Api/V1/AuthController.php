<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle Customer / User Registration
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'terms_accepted_version' => ['nullable', 'string', 'max:20'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'phone' => $validated['phone'] ?? null,
            'timezone' => $request->input('timezone', 'Asia/Jakarta'),
            'locale' => $request->input('locale', 'id'),
            'terms_accepted_version' => $validated['terms_accepted_version'] ?? '1.0',
            'last_login_at' => now(),
        ]);

        // Assign 'customer' role if spatie roles exist
        try {
            $user->assignRole('customer');
        } catch (\Throwable $e) {
            // Ignore if role not seeded yet
        }

        // Authenticate via web guard for Sanctum SPA sessions
        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // Generate token for API clients
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil. Akun Anda siap digunakan.',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'timezone' => $user->timezone,
                'locale' => $user->locale,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }

    /**
     * Handle Customer / User Login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('Email atau password yang Anda masukkan salah.')],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Akun Anda sedang dinonaktifkan atau ditangguhkan.',
                'code' => 'ACCOUNT_SUSPENDED',
            ], 403);
        }

        // Authenticate via web guard for Sanctum SPA sessions
        Auth::login($user, $request->boolean('remember'));
        
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Also generate a personal access token for API/mobile clients
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'timezone' => $user->timezone,
                'locale' => $user->locale,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    /**
     * Get Current Authenticated User profile
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'mentorProfile']);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'timezone' => $user->timezone,
                'locale' => $user->locale,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'mentor_profile' => $user->mentorProfile,
            ],
        ]);
    }

    /**
     * Handle Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user();
        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }
}
