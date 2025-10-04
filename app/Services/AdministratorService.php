<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Class AdministratorService
 *
 * This service handles the business logic for managing administrator users.
 * It includes creating, updating, and deleting administrators, along with handling
 * their profiles and recording audit trails for these actions.
 */
class AdministratorService
{
    /**
     * Store or update an administrator record.
     *
     * This method handles both the creation of a new administrator and the update
     * of an existing one. It validates the request data, manages profile information
     * including photo uploads, and records detailed audit trails for changes.
     *
     * @param \Illuminate\Http\Request $request The request object containing administrator data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function store($request)
    {
        $validated = $request->validated();
        $id = null;

        if ($request->filled('id')) {
            try {
                $id = Crypt::decrypt($request->input('id'));
            } catch (DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }
        }

        // Generate photo path user/profile/08/2025
        $fotoPath = 'user/profile/' . date('m') . '/' . date('Y');

        DB::beginTransaction();
        try {
            if ($id) {
                // Update existing record
                $user = User::with('profile')->find($id);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'Data administrator tidak ditemukan.'], 404);
                }

                // 1. Capture old data for audit trail
                $oldUserData = $user->only(['name', 'email', 'role', 'block']);
                $oldProfileData = $user->profile ? $user->profile->only(['kontak', 'foto']) : [];
                $oldData = array_merge($oldUserData, $oldProfileData);
                // Add a comparable 'aktif' field, which is the inverse of 'block'
                $oldData['aktif'] = !$user->block;

                // Update profile
                $profile = $user->profile ?? new ProfileModel();
                $newFotoPath = null;
                $splitName = StringHelper::splitName($validated['nama']);
                $profileData = [
                    'nama_depan' => $splitName['first_name'],
                    'nama_belakang' => $splitName['last_name'],
                    'kontak' => $validated['kontak'],
                ];

                if (isset($validated['foto'])) {
                    if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
                        Storage::disk('public')->delete($profile->foto);
                    }
                    $newFotoPath = $validated['foto']->store($fotoPath, 'public');
                    $profileData['foto'] = $newFotoPath;
                }
                $profile->update($profileData);

                // Update user
                $userData = [
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                ];

                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $user->update($userData);

                // 2. Prepare new data for audit trail
                $newData = [
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'kontak' => $validated['kontak'],
                    'role' => $validated['role'],
                    'aktif' => $validated['aktif'],
                ];
                if ($newFotoPath) {
                    $newData['foto'] = $newFotoPath;
                }

                // 3. Record detailed audit trail
                $context = ['old' => $oldData, 'new' => $newData];
                AuditTrailService::record('telah memperbarui data administrator: ' . $validated['nama'], $context);
                $message = 'Data administrator berhasil diubah.';
            } else {
                // Create new record
                $splitName = StringHelper::splitName($validated['nama']);
                $newFotoPath = null;
                $profileData = [
                    'nama_depan' => $splitName['first_name'],
                    'nama_belakang' => $splitName['last_name'],
                    'kontak' => $validated['kontak'],
                ];

                if (isset($validated['foto'])) {
                    $newFotoPath = $validated['foto']->store($fotoPath, 'public');
                    $profileData['foto'] = $newFotoPath;
                }
                $profile = ProfileModel::create($profileData);

                $user = User::create([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['role'],
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                    'reactivation' => '0',
                    'profile_id' => $profile->id,
                    'profile_status' => '0'
                ]);

                // Prepare new data for audit trail
                $newData = [
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'kontak' => $validated['kontak'],
                    'role' => $validated['role'],
                    'aktif' => $validated['aktif'],
                ];
                if ($newFotoPath) {
                    $newData['foto'] = $newFotoPath;
                }

                // Record detailed audit trail
                $context = ['old' => [], 'new' => $newData];
                AuditTrailService::record('telah menambahkan data administrator: ' . $validated['nama'], $context);
                $message = 'Data administrator berhasil ditambahkan.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving administrator: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Delete an administrator record.
     *
     * This method deletes an administrator and their associated profile, including
     * the profile photo from storage. It ensures that the action is recorded in the
     * audit trail before deleting the data.
     *
     * @param string $id The encrypted ID of the administrator to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            try {
                $decryptedId = Crypt::decrypt($id);
            } catch (DecryptException $e) {
                Log::warning('Gagal mendekripsi ID administrator untuk dihapus: ' . $id, ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }

            $user = User::with('profile')->find($decryptedId);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Data administrator tidak ditemukan.'], 404);
            }

            // Capture data for audit trail before deletion
            $userName = $user->name;
            $oldData = $user->toArray(); // Captures the model and its loaded relations

            // Safely delete profile and its photo if it exists
            if ($user->profile) {
                if ($user->profile->foto && Storage::disk('public')->exists($user->profile->foto)) {
                    Storage::disk('public')->delete($user->profile->foto);
                }
                $user->profile->delete();
            }

            // Record the deletion in the audit trail
            $context = [
                'old' => $oldData,
                'new' => [], // 'new' is empty for a delete action
            ];
            AuditTrailService::record('telah menghapus data administrator: ' . $userName, $context);

            $user->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data administrator berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting administrator: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
