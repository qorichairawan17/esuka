<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;

class ResetPasswordController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Reset Password - ' . config('app.name'),
        ];
        return view('auth.forgot-password', $data);
    }

    public function reset(Request $request, $token)
    {
        $resetRecord = DB::table('password_reset_tokens')->where('token', $token)->first();

        // Check if the token is valid and not expired.
        // The expiration time is read from the auth config file.
        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return redirect()->route('auth.forgot-password')->with('error', 'Token reset password tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth.forgot-password', [
            'token' => $token,
            'email' => $resetRecord->email,
        ]);
    }

    public function send(SendResetLinkRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email Kamu tidak terdaftar.',
            ]);
        }

        try {
            // Use a transaction to ensure atomicity of token creation.
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

            // Queue the email to be sent in the background.
            Mail::to($user)->queue(new ResetPasswordMail('Reset Password', $user, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Tautan untuk reset password telah dikirim ke email Kamu.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending password reset email: ' . $e->getMessage(), ['email' => $email]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permintaan Kamu. Silakan coba lagi nanti.',
            ], 500);
        }
    }

    public function save(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $resetRecord = DB::table('password_reset_tokens')->where('email', $validated['email'])->where('token', $validated['token'])->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return response()->json(['success' => false, 'message' => 'Token reset password tidak valid atau sudah kedaluwarsa.'], 400);
        }

        // Use a transaction to ensure atomicity of password update and token deletion.
        DB::transaction(function () use ($validated) {
            User::where('email', $validated['email'])->update(['password' => Hash::make($validated['password'])]);
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Password Kamu berhasil direset. Silakan login dengan password baru.',
            'redirect' => route('app.signin'),
        ]);
    }
}
