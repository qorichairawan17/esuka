<?php

namespace App\Services;

use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Support\Str;
use App\Mail\ActivateAccountMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User\UserActivationModel;

class AuthService
{
    public function login($request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar !',
            ]);
        }

        if ($user->reactivation == 1) {
            $newPassword = Str::random(8);
            $user->update(['password' => Hash::make($newPassword)]);

            $token = Str::random(64);
            UserActivationModel::create([
                'user_id'   => $user->id,
                'token'     => $token,
                'expires_at' => now()->addMinutes(60),
            ]);

            $activationUrl = route('auth.activate-account', ['token' => $token]);
            Mail::to($user->email)->queue(new ActivateAccountMail('Aktivasi Ulang Akun', $user, $activationUrl, $newPassword));

            // Log the successful registration.
            Log::info('User registered successfully. Reactivation email sent.', ['user_id' => $user->id, 'email' => $user->email]);

            // Send the activation email *after* the transaction has been successfully committed.
            return response()->json([
                'success' => false,
                'message' => 'Email kamu terdaftar. Silakan aktivasi ulang akun kamu !',
            ]);
        }

        // Assuming 'block' = 1 means the user is blocked.
        if ($user->block == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Kamu terblokir !',
            ]);
        }

        // Auth::attempt handles password verification and logs the user in.
        // If it fails here, it's because of a wrong password since we already found the user.
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah !',
            ]);
        }

        $request->session()->regenerate();

        $intended = match ($user->role) {
            RoleEnum::Superadmin->value, RoleEnum::Administrator->value => '/dashboard/panel-admin',
            default => '/dashboard/panel-pengguna'
        };

        AuditTrailService::record('telah login ke sistem aplikasi', [], $user);
        // The 'redirect' key must be a URL string for the frontend JavaScript to use.
        // redirect()->intended() returns a RedirectResponse object. We need to get the URL from it.
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil !',
            'redirect' => redirect()->intended($intended)->getTargetUrl()
        ]);
    }

    public function register($request)
    {
        // Get validated data once to avoid calling it multiple times.
        $validated = $request->validated();

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // If the user exists but is inactive (block == 1), delete the old record
            if ($existingUser->block == 1 && $existingUser->reactivation == 0) {
                DB::transaction(function () use ($existingUser) {
                    UserActivationModel::where('user_id', $existingUser->id)->delete();
                    ProfileModel::where('id', $existingUser->profile_id)->delete();
                    $existingUser->forceDelete();
                });
                Log::info('Removed inactive user record to allow re-registration.', ['email' => $validated['email']]);
            } else {
                // If the user exists and is active, prevent re-registration.
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar dan aktif. Silakan login.',
                ]);
            }
        }

        $request->validated(['email' => 'unique:users,email'], [
            'email.unique' => 'Email sudah terdaftar !',
        ]);

        try {
            // The transaction should only handle database operations.
            // It returns the created user and token to be used later.
            $data = DB::transaction(function () use ($validated) {
                $profile = ProfileModel::create([
                    'nama_depan' => $validated['namaDepan'],
                    'nama_belakang' => $validated['namaBelakang'],
                ]);

                $user = User::create([
                    'name' => $validated['namaDepan'] . ' ' . $validated['namaBelakang'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => RoleEnum::User->value,
                    'block' => '1', // User is blocked until activation
                    'reactivation' => '0',
                    'profile_id' => $profile->id,
                    'profile_status' => '0',
                    'privacy_policy_agreed_at' => now(),
                ]);

                $token = Str::random(64);
                UserActivationModel::create([
                    'user_id'   => $user->id,
                    'token'     => $token,
                    'expires_at' => now()->addMinutes(60),
                ]);

                return ['user' => $user, 'token' => $token];
            });

            $user = $data['user'];

            // Send the activation email *after* the transaction has been successfully committed.
            $activationUrl = route('auth.activate-account', ['token' => $data['token']]);
            Mail::to($user->email)->queue(new ActivateAccountMail('Aktivasi Akun', $user, $activationUrl));

            // Log the successful registration.
            Log::info('User registered successfully. Activation email sent.', ['user_id' => $user->id, 'email' => $user->email]);

            // Redirect with a more accurate message. The user cannot log in yet.
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan periksa email Anda untuk mengaktifkan akun.',
                'redirect' => route('app.signin')
            ]);
        } catch (\Exception $e) {
            Log::error('Registration failed for email: ' . $validated['email'] . '. Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except('password'),
            ]);
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Terjadi kesalahan pada server.'
            ], 500);
        }
    }
}
