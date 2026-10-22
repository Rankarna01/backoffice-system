<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update User Profile Information (Name, Phone, Timezone)
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'phone' => isset($validated['phone']) ? trim($validated['phone']) : $user->phone,
            'timezone' => $validated['timezone'] ?? $user->timezone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
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
        ]);
    }

    /**
     * Update User Email Address (Requires current password verification)
     */
    public function updateEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email baru wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.required' => 'Password saat ini diperlukan untuk verifikasi keamanan.',
        ]);

        if (! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password saat ini yang Anda masukkan salah.'],
            ]);
        }

        $user->update([
            'email' => strtolower(trim($validated['email'])),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email akun berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * Update User Password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'different:current_password'],
            'new_password_confirmation' => ['required', 'string', 'same:new_password'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.different' => 'Password baru tidak boleh sama dengan password saat ini.',
            'new_password_confirmation.same' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini yang Anda masukkan salah.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah. Silakan gunakan password baru untuk login berikutnya.',
        ]);
    }
}
