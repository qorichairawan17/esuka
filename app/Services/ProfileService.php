<?php

namespace App\Service;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class ProfileService
{
    public function update($request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = Auth::user()->load('profile');

            // 1. Capture old data for audit trail
            $oldUserData = $user->only(['name', 'email']);
            $oldProfileData = $user->profile ? $user->profile->only(['nama_depan', 'nama_belakang', 'kontak', 'tanggal_lahir', 'jenis_kelamin', 'alamat']) : [];
            $oldData = array_merge($oldUserData, $oldProfileData);
            if (!empty($oldData['tanggal_lahir'])) {
                $oldData['tanggal_lahir'] = Carbon::parse($oldData['tanggal_lahir'])->format('d-m-Y');
            }

            // 2. Update user and profile data
            // The user's name is a combination of first and last name
            $user->name = trim($validated['namaDepan'] . ' ' . $validated['namaBelakang']);
            $user->email = $validated['email'];

            // Check existing foto on profile
            if ($user->profile && $user->profile->foto != null && $user->profile_status == 0) {
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

            // 3. Record detailed audit trail
            $context = [
                'old' => $oldData,
                'new' => $validated, // The validated request data represents the new state
            ];
            AuditTrailService::record('telah memperbarui profil', $context);

            Log::info('User profile updated successfully.', ['user_id' => $user->id]);

            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user profile: ' . $e->getMessage(), ['user_id' => Auth::user()->id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updatePhoto($request)
    {
        $request->validated();
        DB::beginTransaction();
        try {
            // Generate photo path user/profile/08/2025
            $fotoPath = 'user/profile/' . date('m') . '/' . date('Y');

            $file = $request->file('foto');
            $user = User::with('profile')->find(Auth::user()->id);

            if (!$user) {
                Log::error('User not found when updating profile photo.', ['user_id' => Auth::user()->id]);
                return response()->json(['message' => 'Data pengguna tidak ditemukan.'], 404);
            }
            // 1. Capture old data
            $oldPhotoPath = $user->profile->foto ?? null;

            // 2. Update photo
            if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                Storage::disk('public')->delete($oldPhotoPath);
            }

            $newPhotoPath = $file->store($fotoPath, 'public');

            // Check existing foto on profile
            if ($user->profile && $user->profile->foto != null && $user->profile_status == 0) {
                $user->update(['profile_status' => 1]);
            }

            // Ensure profile exists before updating
            $user->profile()->updateOrCreate(
                ['id' => $user->id],
                ['foto' => $newPhotoPath]
            );
            DB::commit();

            // 3. Record detailed audit trail
            $context = [
                'old' => ['foto' => $oldPhotoPath],
                'new' => ['foto' => $newPhotoPath],
            ];
            AuditTrailService::record('telah memperbarui foto profil', $context);

            Log::info('Profile photo updated successfully.', ['user_id' => $user->id]);

            return response()->json(['message' => 'Foto profil berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating profile photo: ' . $e->getMessage(), ['user_id' => Auth::user()->id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat memperbarui foto profil.'], 500);
        }
    }

    public function updatePassword($request)
    {
        $request->validated();
        try {
            $user = Auth::user();
            $user->password = Hash::make($request->input('passwordBaru'));
            $user->save();
            Log::info('User changed their password successfully.', ['user_id' => $user->id]);
            AuditTrailService::record('telah mengubah password');

            return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
        } catch (\Exception $e) {
            Log::error('Error updating password for user: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah password.'], 500);
        }
    }

    public function destroy()
    {
        $user = Auth::user()->load('profile'); // Eager load profile
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Data pengguna tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            // 1. Get all registrations for the user
            $pendaftarans = PendaftaranSuratKuasaModel::with(['pihak', 'register', 'pembayaran'])
                ->where('user_id', $user->id)
                ->get();

            // 2. Prepare detailed data for the audit trail BEFORE deletion
            $oldDataForAudit = [
                'user_details' => $user->toArray(), // Includes profile data
                'registrations_data' => $pendaftarans->toArray(), // Includes all related registration data
            ];
            $context = [
                'old' => $oldDataForAudit,
                'new' => [], // 'new' is empty for a delete action
            ];

            // Record the audit trail. We pass the user object explicitly as it's the subject of the action.
            AuditTrailService::record('telah menghapus akunnya sendiri secara permanen', $context, $user);

            $localDirectoriesToDelete = [];
            $publicFilesToDelete = [];

            // 2. Collect directories and files to be deleted
            foreach ($pendaftarans as $pendaftaran) {
                $registrationFiles = [];
                $docColumns = [
                    'edoc_kartu_tanda_penduduk',
                    'edoc_kartu_tanda_anggota',
                    'edoc_kartu_tanda_pegawai',
                    'edoc_berita_acara_sumpah',
                    'edoc_surat_tugas',
                    'edoc_surat_kuasa',
                ];
                foreach ($docColumns as $column) {
                    if (!empty($pendaftaran->$column)) {
                        $registrationFiles[] = $pendaftaran->$column;
                    }
                }

                if ($pendaftaran->pembayaran && !empty($pendaftaran->pembayaran->bukti_pembayaran)) {
                    $registrationFiles[] = $pendaftaran->pembayaran->bukti_pembayaran;
                }

                if ($pendaftaran->register && !empty($pendaftaran->register->path_file)) {
                    $registrationFiles[] = $pendaftaran->register->path_file;
                }

                // Get unique directories from the collected file paths for this registration
                $directories = array_map('dirname', $registrationFiles);
                foreach ($directories as $dir) {
                    $localDirectoriesToDelete[] = $dir;
                }
            }

            // 3. Collect user's profile photo (public disk)
            if ($user->profile && !empty($user->profile->foto)) {
                $publicFilesToDelete[] = $user->profile->foto;
            }

            // 4. Delete all collected directories and files from their respective disks
            $uniqueLocalDirectories = array_unique($localDirectoriesToDelete);
            foreach ($uniqueLocalDirectories as $directory) {
                if ($directory !== '.' && Storage::disk('local')->exists($directory)) {
                    Storage::disk('local')->deleteDirectory($directory);
                }
            }
            foreach ($publicFilesToDelete as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            // 5. Delete database records for each registration
            foreach ($pendaftarans as $pendaftaran) {
                $pendaftaran->register()->delete();
                $pendaftaran->pembayaran()->delete();
                $pendaftaran->pihak()->delete();
                $pendaftaran->forceDelete();
            }

            // 6. Delete user's profile and the user itself
            $user->profile()->delete();
            $user->delete();

            DB::commit();

            Log::info('User account and all related data have been successfully deleted and audited.', ['user_id' => $user->id]);
            return response()->json(['success' => true, 'message' => 'Akun berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user account: ' . $e->getMessage(), ['user_id' => $user->id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus akun.'], 500);
        }
    }
}
