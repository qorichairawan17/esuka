<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengaturan\PembayaranPnbpModel;
use App\Models\Pengaturan\PejabatStrukturalModel;

class AplikasiService
{
    /**
     * Store or update the main application settings.
     *
     * This method handles the validation of application settings data,
     * processes the logo upload (storing the new file and deleting the old one),
     * and saves the settings to the database within a transaction.
     * On success, it clears the application info cache and records an audit trail.
     *
     * @param \App\Http\Requests\Pengaturan\AplikasiRequest $request The request object containing validated application data.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeApp($request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $aplikasi = AplikasiModel::find(1);
        $oldData = $aplikasi ? $aplikasi->toArray() : [];
        $newLogoPath = null;

        DB::beginTransaction();
        try {
            if ($request->hasFile('logo')) {
                $newLogoPath = $request->file('logo')->store('logo', 'public');
                $validatedData['logo'] = $newLogoPath;
            }

            $record = AplikasiModel::updateOrCreate(['id' => 1], $validatedData);

            if ($newLogoPath && ($oldData['logo'] ?? null)) {
                Storage::disk('public')->delete($oldData['logo']);
            }

            DB::commit();

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            // forget cache
            cache()->forget('infoApp');
            AuditTrailService::record("telah {$action} pengaturan aplikasi", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            Log::error('Gagal menyimpan pengaturan aplikasi: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Store or update the payment and PNBP (Non-Tax State Revenue) settings.
     *
     * This method validates the payment settings, handles the upload of the
     * bank logo and QRIS image, and saves the data within a database transaction.
     * It replaces old files with new ones upon successful update.
     * An audit trail is recorded for the action.
     *
     * @param \App\Http\Requests\Pengaturan\PembayaranPnbpRequest $request The request object containing validated payment data.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storePayment($request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $pembayaran = PembayaranPnbpModel::find(1);
        $oldData = $pembayaran ? $pembayaran->toArray() : [];
        $newLogoBankPath = null;
        $newQrisPath = null;

        DB::beginTransaction();
        try {
            $dataToUpdate = [
                'nama_bank' => $validatedData['namaBank'],
                'nomor_rekening' => $validatedData['nomorRekening'],
            ];

            if ($request->hasFile('logoBank')) {
                $newLogoBankPath = $request->file('logoBank')->store('pengaturan/pembayaran', 'public');
                $dataToUpdate['logo_bank'] = $newLogoBankPath;
            }

            if ($request->hasFile('qris')) {
                $newQrisPath = $request->file('qris')->store('pengaturan/pembayaran', 'public');
                $dataToUpdate['qris'] = $newQrisPath;
            }

            $record = PembayaranPnbpModel::updateOrCreate(['id' => 1], $dataToUpdate);

            if ($newLogoBankPath && ($oldData['logo_bank'] ?? null)) {
                Storage::disk('public')->delete($oldData['logo_bank']);
            }

            if ($newQrisPath && ($oldData['qris'] ?? null)) {
                Storage::disk('public')->delete($oldData['qris']);
            }

            DB::commit();

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            AuditTrailService::record("telah {$action} pengaturan pembayaran & PNBP", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newLogoBankPath) {
                Storage::disk('public')->delete($newLogoBankPath);
            }
            if ($newQrisPath) {
                Storage::disk('public')->delete($newQrisPath);
            }

            Log::error('Gagal menyimpan pengaturan pembayaran dan PNBP: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Store or update the structural official's data.
     *
     * This method validates the data for structural officials, handles the upload
     * of their photos, and saves the information within a database transaction.
     * It replaces old photos with new ones upon successful update and rolls back
     * file uploads on failure. An audit trail is recorded for the action.
     *
     * @param \App\Http\Requests\Pengaturan\PejabatStrukturalRequest $request The request object containing validated officials' data.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storePejabatStruktural($request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $pejabat = PejabatStrukturalModel::find(1);
        $oldData = $pejabat ? $pejabat->toArray() : [];

        $dataToUpdate = [
            'ketua' => $validatedData['ketua'],
            'wakil_ketua' => $validatedData['wakil_ketua'],
            'panitera' => $validatedData['panitera'],
            'sekretaris' => $validatedData['sekretaris'],
        ];

        $newlyUploadedPaths = [];

        DB::beginTransaction();
        try {
            $officials = ['ketua', 'wakil_ketua', 'panitera', 'sekretaris'];
            foreach ($officials as $official) {
                $fileKey = 'foto_' . $official;
                if ($request->hasFile($fileKey)) {
                    $path = $request->file($fileKey)->store('pengaturan/pejabat', 'public');
                    $dataToUpdate[$fileKey] = $path;
                    $newlyUploadedPaths[$fileKey] = $path;
                }
            }

            $record = PejabatStrukturalModel::updateOrCreate(['id' => 1], $dataToUpdate);

            foreach ($newlyUploadedPaths as $key => $path) {
                if (isset($oldData[$key]) && $oldData[$key]) {
                    Storage::disk('public')->delete($oldData[$key]);
                }
            }

            DB::commit();

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            // forget cache
            cache()->forget('pejabatStruktural');
            AuditTrailService::record("telah {$action} pengaturan pejabat struktural", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($newlyUploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Gagal menyimpan data pejabat struktural: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
