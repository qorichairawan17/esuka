<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\User\UserActivationModel;

class ActivationController extends Controller
{
    public function activate(Request $request)
    {
        $activation = UserActivationModel::where('token', $request->token)->first();

        if (!$activation) {
            return redirect()->route('app.signin')->with('error', 'Token aktivasi tidak valid atau sudah digunakan.');
        }

        if ($activation->expires_at < now()) {
            // The token is expired. We should delete it and inform the user.
            $activation->delete();
            return redirect()->route('app.signin')->with('error', 'Token aktivasi sudah kedaluwarsa. Silakan login kembali untuk akvitasi ulang');
        }

        // Wrap database mutations in a transaction for atomicity.
        try {
            DB::transaction(function () use ($activation) {
                $user = $activation->user;
                $user->update(['email_verified_at' => now(), 'block' => '0', 'reactivation' => '0']);

                // Delete the activation token so it cannot be used again.
                $activation->delete();

                Log::info('User account activated successfully.', ['user_id' => $user->id]);
            });

            return redirect()->route('app.signin')->with('success', 'Akun Kamu berhasil diaktivasi, silakan login.');
        } catch (\Exception $e) {
            Log::error('Account activation failed during transaction.', [
                'activation_id' => $activation->id,
                'user_id' => $activation->user_id,
                'exception' => $e,
            ]);
            report($e);

            return redirect()->route('app.signin')->with('error', 'Gagal mengaktivasi akun. Terjadi kesalahan pada server.');
        }
    }

    public function emailTest()
    {
        Mail::raw('Tes kirim email dari Laravel', function ($message) {
            $message->to('qorichairawan17@gmail.com')
                ->subject('Tes Email');
        });
        return response()->json(['success' => true, 'message' => 'Sending email test']);
    }
}
