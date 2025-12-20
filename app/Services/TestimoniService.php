<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Testimoni\TestimoniModel;

class TestimoniService
{
    /**
     * Store or update a testimonial for the authenticated user.
     *
     * This method uses `updateOrCreate` to handle both the creation of a new testimonial
     * and the updating of an existing one within a database transaction.
     * It also records an audit trail for the action.
     *
     * @param array $validated The validated request data containing 'rating' and 'pesan'.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function store($validated): JsonResponse
    {
        DB::beginTransaction();
        try {
            $existingTestimoni = TestimoniModel::where('user_id', Auth::id())->first();
            $oldData = $existingTestimoni ? $existingTestimoni->only(['rating', 'testimoni']) : [];

            $testimoni = TestimoniModel::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'rating' => $validated['rating'],
                    'testimoni' => $validated['pesan'],
                ]
            );

            $action = $testimoni->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $context = [
                'old' => $oldData,
                'new' => [
                    'rating' => $validated['rating'],
                    'testimoni' => $validated['pesan'],
                ]
            ];

            AuditTrailService::record("telah {$action} testimoni", $context);
            DB::commit();

            // forget cache
            cache()->forget('testimoni');
            return response()->json(['success' => true, 'message' => 'Terima kasih, testimoni Kamu telah disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving testimoni: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan testimoni.'], 500);
        }
    }

    /**
     * Retrieve a specific testimonial for editing.
     *
     * This method decrypts the provided ID, finds the corresponding testimonial,
     * and returns it as a JSON response. It handles potential decryption and model not found exceptions.
     *
     * @param \Illuminate\Http\Request $request The request object containing the encrypted testimonial ID.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the testimonial data on success, or an error message on failure.
     */
    public function edit($request): JsonResponse
    {
        try {
            $decryptedId = Crypt::decrypt($request->id);
            $testimoni = TestimoniModel::findOrFail($decryptedId);
            return response()->json($testimoni);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Gagal mendekripsi ID testimoni: ' . $request->id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan.'], 404);
        }
    }

    /**
     * Update a specific testimonial, typically by an administrator.
     *
     * This method finds the testimonial by its encrypted ID, updates its content
     * and publication status within a database transaction, and records the change
     * in the audit trail.
     *
     * @param array $validated The validated request data containing 'rating', 'pesan', and optionally 'publish'.
     * @param string $id The encrypted ID of the testimonial to update.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function update($validated, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $decryptedId = Crypt::decrypt($id);
            $testimoni = TestimoniModel::with('user')->findOrFail($decryptedId);

            $oldData = $testimoni->only(['rating', 'testimoni', 'publish_at']);

            $testimoni->update([
                'rating' => $validated['rating'],
                'testimoni' => $validated['pesan'],
                'publish_at' => isset($validated['publish']) && $validated['publish'] ? now() : null,
            ]);

            $newData = [
                'rating' => $validated['rating'],
                'testimoni' => $validated['pesan'],
                'publish_at' => $testimoni->publish_at ? $testimoni->publish_at->toDateTimeString() : null,
            ];

            $context = ['old' => $oldData, 'new' => $newData];
            AuditTrailService::record("telah memperbarui testimoni dari pengguna: " . $testimoni->user->name, $context);

            DB::commit();

            Log::info('Testimoni berhasil diperbarui', ['id' => $decryptedId, 'user_id' => $testimoni->user_id]);
            // forget cache
            cache()->forget('testimoni');
            return response()->json(['success' => true, 'message' => 'Testimoni berhasil diperbarui.']);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'ID testimoni tidak valid.'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating testimoni: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
