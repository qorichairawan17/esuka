<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     * The action ('login' or 'register') is passed via query string.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
        $action = $request->query('action', 'login');
        session(['google_auth_action' => $action]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $action = session()->pull('google_auth_action', 'login');

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($action === 'register') {
                return $this->handleRegistrationCallback($googleUser, $user);
            }

            // Default action is 'login'
            return $this->handleLoginCallback($googleUser, $user);
        } catch (InvalidStateException $e) {
            Log::warning('Google Socialite InvalidStateException: ' . $e->getMessage());
            return redirect()->route('app.signin')->with('error', 'Sesi otentikasi tidak valid. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Google Socialite callback error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('app.signin')->with('error', 'Terjadi kesalahan saat otentikasi dengan Google.');
        }
    }

    /**
     * Handle the callback for a registration attempt via Google.
     *
     * @param \Laravel\Socialite\Contracts\User $googleUser
     * @param \App\Models\User|null $existingUser
     * @return RedirectResponse
     */
    private function handleRegistrationCallback($googleUser, ?User $existingUser): RedirectResponse
    {
        if ($existingUser) {
            return redirect()->route('app.signup')->with('error', 'Email Google Anda sudah terdaftar! Silakan login.');
        }

        $newUser = DB::transaction(function () use ($googleUser) {
            $splitName = StringHelper::splitName($googleUser->getName());
            $profile = ProfileModel::create([
                'nama_depan' => $splitName['first_name'],
                'nama_belakang' => $splitName['last_name'],
            ]);

            return User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(16)), // Create a random password
                'role' => RoleEnum::User->value,
                'block' => '0',
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'profile_id' => $profile->id,
                'profile_status' => '0',
            ]);
        });

        Auth::login($newUser);

        return redirect()->intended('/dashboard/panel-pengguna');
    }

    /**
     * Handle the callback for a login attempt via Google.
     *
     * @param \Laravel\Socialite\Contracts\User $googleUser
     * @param \App\Models\User|null $user
     * @return RedirectResponse
     */
    private function handleLoginCallback($googleUser, ?User $user): RedirectResponse
    {
        if (!$user) {
            return redirect()->route('app.signin')->with('error', 'Akun dengan email Google tersebut tidak terdaftar');
        }
        if ($user->google_id == null) {
            return redirect()->route('app.signin')->with('error', 'Akun Anda belum terhubung dengan Google');
        }

        // Update google_id and avatar if they are missing, then log in.
        $user->update([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]);

        Auth::login($user);
        $intended = match ($user->role) {
            RoleEnum::Superadmin->value, RoleEnum::Administrator->value => '/dashboard/panel-administrator',
            default => '/dashboard/panel-pengguna'
        };
        AuditTrailService::record('login pada sistem aplikasi melalui Google pada ' . now()->format('d F Y, h:i A'));
        return redirect()->intended($intended);
    }

    /**
     * Link the currently authenticated user's account with their Google account.
     *
     * @return RedirectResponse
     */
    public function linked(): RedirectResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $googleUser = Socialite::driver('google')->user();

            // Check if another user has already linked this Google ID.
            $existingUser = User::where('google_id', $googleUser->getId())->where('id', '!=', $user->id)->first();
            if ($existingUser) {
                return redirect()->route('profile.index')->with('error', 'Akun Google ini sudah terhubung dengan pengguna lain.');
            }

            // Verify that the Google email matches the user's email.
            if ($user->email !== $googleUser->getEmail()) {
                return redirect()->route('profile.index')->with('error', 'Email Google tidak sama dengan email pada akun Anda.');
            }

            // Update the user's profile with Google info.
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            AuditTrailService::record('telah menautkan akun Google pada ' . now()->format('d F Y, h:i A'));

            return redirect()->route('profile.index')->with('success', 'Akun Google berhasil ditautkan!');
        } catch (\Exception $e) {
            Log::error('Google Socialite linking error: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->route('profile.index')->with('error', 'Terjadi kesalahan saat mencoba menautkan akun Google Anda.');
        }
    }
}
