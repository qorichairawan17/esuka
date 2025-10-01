<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordService
{
    /**
     * Show the password reset form if the token is valid.
     *
     * @param string $token The password reset token.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function reset($token)
    {
        $resetRecord = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return redirect()->route('auth.forgot-password')->with('error', 'Token reset password tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth.forgot-password', [
            'token' => $token,
            'email' => $resetRecord->email,
        ]);
    }

    /**
     * Send a password reset link to the user's email.
     *
     * @param array $validated The validated request data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function send($validated): JsonResponse
    {
        $email = $validated['email'];

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email Kamu tidak terdaftar.',
            ]);
        }

        try {
            $token = DB::transaction(function () use ($email) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                $token = Str::random(64);
                DB::table('password_reset_tokens')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]);
                return $token;
            });

            $resetUrl = route('auth.forgot-password.reset', ['token' => $token]);

            Mail::to($user)->queue(new ResetPasswordMail('Reset Password', $user, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Tautan reset password telah dikirim ke email Kamu.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending password reset email: ' . $e->getMessage(), ['email' => $email]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permintaan Kamu. Silakan coba lagi nanti.',
            ], 500);
        }
    }

    /**
     * Save the new password.
     *
     * @param array $validated The validated request data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function save($validated): JsonResponse
    {
        $resetRecord = DB::table('password_reset_tokens')->where('email', $validated['email'])->where('token', $validated['token'])->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return response()->json(['success' => false, 'message' => 'Token reset password tidak valid atau sudah kedaluwarsa.'], 400);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna dengan alamat email tersebut tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->update(['password' => Hash::make($validated['password'])]);
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
        });

        AuditTrailService::record('telah mereset passwordnya', [], $user);

        return response()->json([
            'success' => true,
            'message' => 'Password Kamu telah berhasil direset. Silakan login dengan password baru Kamu.',
            'redirect' => route('app.signin'),
        ]);
    }
}
