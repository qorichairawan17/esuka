<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Class PaniteraService
 *
 * This service handles the business logic for managing panitera data.
 * It includes creating, updating, and deleting panitera records,
 * and recording audit trails for these actions.
 */
class PaniteraService
{
    /**
     * Store or update a panitera record.
     *
     * This method handles both the creation of a new panitera and the update
     * of an existing one. It validates the request data and records detailed
     * audit trails for changes.
     *
     * @param \Illuminate\Http\Request $request The request object containing panitera data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function store($request): JsonResponse
    {
        $validatedData = $request->validated();
        $id = null;

        if ($request->filled('id')) {
            try {
                $id = Crypt::decrypt($request->input('id'));
            } catch (DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }
        }

        DB::beginTransaction();
        try {
            if ($id) {
                $panitera = PaniteraModel::find($id);
                if (!$panitera) {
                    return response()->json(['success' => false, 'message' => 'Data panitera tidak ditemukan.'], 404);
                }

                $oldData = $panitera->toArray();
                $panitera->update($validatedData);

                $context = ['old' => $oldData, 'new' => $validatedData];
                AuditTrailService::record('telah memperbarui data panitera: ' . $validatedData['nama'], $context);
                $message = 'Data panitera berhasil diubah.';
            } else {
                $validatedData['created_by'] = Auth::id();
                PaniteraModel::create($validatedData);

                $context = ['old' => [], 'new' => $validatedData];
                AuditTrailService::record('telah menambahkan data panitera: ' . $validatedData['nama'], $context);

                $message = 'Data panitera berhasil ditambahkan.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving panitera: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Delete a panitera record.
     *
     * This method deletes a panitera. It ensures that the action is recorded
     * in the audit trail before deleting the data.
     *
     * @param string $id The encrypted ID of the panitera to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            try {
                $decryptedId = Crypt::decrypt($id);
            } catch (DecryptException $e) {
                Log::warning('Gagal mendekripsi ID panitera untuk dihapus: ' . $id, ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }

            $panitera = PaniteraModel::find($decryptedId);

            if (!$panitera) {
                return response()->json(['success' => false, 'message' => 'Data panitera tidak ditemukan.'], 404);
            }

            $oldData = $panitera->toArray();
            $paniteraName = $panitera->nama;

            $context = ['old' => $oldData, 'new' => []];
            AuditTrailService::record('telah menghapus data panitera: ' . $paniteraName, $context);

            $panitera->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data panitera berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting panitera: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
