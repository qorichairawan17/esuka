<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\AuditTrail\AuditTrailModel;
use App\Http\Requests\Profile\UpdatePhotoRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;

class ProfileController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Dashboard', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.admin'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Profil', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Profil - ' . config('app.name'),
            'pageTitle' => "Profil Saya",
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => Auth::user(),
            'auditTrail' => AuditTrailModel::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.pengguna.profil', $data);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // The user's name is a combination of first and last name
            $user->name = trim($validated['namaDepan'] . ' ' . $validated['namaBelakang']);
            $user->email = $validated['email'];

            // Check existing foto on profile
            if ($user->profile->foto != null && $user->profile_status == 0) {
                $user->profile_status = 1;
            }
            $user->save();

            // Update the profile table, create if it doesn't exist
            $user->profile()->updateOrCreate(['id' => $user->id], [
                'nama_depan' => $validated['namaDepan'],
                'nama_belakang' => $validated['namaBelakang'],
                'kontak' => $validated['kontak'],
                'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', $validated['tanggalLahir'])->format('Y-m-d'),
                'jenis_kelamin' => $validated['jenisKelamin'],
                'alamat' => $validated['alamat'],
            ]);

            DB::commit();

            Log::info('User profile updated successfully.', ['user_id' => $user->id]);
            AuditTrailService::record('memperbarui profil pada ' . now()->format('d F Y, h:i A'));

            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user profile: ' . $e->getMessage(), ['user_id' => Auth::user()->id, 'trace' => $e->getTraceAsString()]);
            // return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui profil.'], 500);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updatePhoto(UpdatePhotoRequest $request): JsonResponse
    {
        $request->validated();
        try {

            // Generate photo path user/profile/08/2025
            $fotoPath = 'user/profile/' . date('m') . '/' . date('Y');

            $file = $request->file('foto');
            $user = User::with('profile')->find(Auth::user()->id);

            if (!$user) {
                Log::error('User not found when updating profile photo.', ['user_id' => Auth::user()->id]);
                return response()->json(['message' => 'Data pengguna tidak ditemukan.'], 404);
            }

            if ($user->profile && $user->profile->foto && Storage::disk('public')->exists($user->profile->foto)) {
                Storage::disk('public')->delete($user->profile->foto);
            }

            $foto = $file->store($fotoPath, 'public');

            // Check existing foto on profile
            if ($user->profile->foto != null && $user->profile_status == 0) {
                $user->update(['profile_status' => 1]);
            }
            $user->profile()->update(['foto' => $foto]);
            Log::info('Profile photo updated successfully.', ['user_id' => $user->id]);
            AuditTrailService::record('memperbarui foto profil pada ' . now()->format('d F Y, h:i A'));

            return response()->json(['message' => 'Foto profil berhasil diubah']);
        } catch (\Exception $e) {
            Log::error('Error updating profile photo: ' . $e->getMessage(), ['user_id' => Auth::user()->id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat memperbarui foto profil.'], 500);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->validated();

        try {
            $user = Auth::user();

            // Update password
            $user->password = Hash::make($request->input('passwordBaru'));
            $user->save();

            Log::info('User changed their password successfully.', ['user_id' => $user->id]);


            return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
        } catch (\Exception $e) {
            Log::error('Error updating password for user: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah password.'], 500);
        }
    }
}
