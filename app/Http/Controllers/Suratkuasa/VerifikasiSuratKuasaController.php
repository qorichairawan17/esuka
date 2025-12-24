<?php

namespace App\Http\Controllers\Suratkuasa;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enum\StatusSuratKuasaEnum;
use App\Services\AuditTrailService;
use App\Enum\TahapanSuratKuasaEnum;
use App\Mail\RejectSuratKuasaMail;
use App\Mail\ApproveSuratKuasaMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Notifications\SuratKuasaStatusNotification;
use App\Jobs\GenerateBarcodeSuratKuasaPDF;
use App\Http\Requests\SuratKuasa\ApproveSuratKuasaRequest;
use App\Http\Requests\SuratKuasa\RejectSuratKuasaRequest;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;


class VerifikasiSuratKuasaController extends Controller
{
    /**
     * Approve a power of attorney registration.
     *
     * @param ApproveSuratKuasaRequest $request
     * @return JsonResponse
     */
    public function approve(ApproveSuratKuasaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Decrypt the id
            $id = Crypt::decrypt($validated['id']);
            $pendaftaran = PendaftaranSuratKuasaModel::with('user')->findOrFail($id);

            // Capture old data for audit trail
            $oldData = $pendaftaran->only(['tahapan', 'status', 'keterangan']);

            // Check if the power of attorney has already been registered
            if ($pendaftaran->register) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Surat kuasa ini sudah diregistrasi sebelumnya.'], 409);
            }

            // Check if the number of the power of attorney has already been used in the current year
            $currentYear = Carbon::now()->year;
            $isDuplicate = RegisterSuratKuasaModel::where('nomor_surat_kuasa', $validated['nomor_surat_kuasa'])
                ->whereYear('created_at', $currentYear)
                ->exists();

            if ($isDuplicate) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Nomor surat kuasa sudah digunakan tahun ini. Silakan gunakan nomor lain.'], 409);
            }

            // Create a new record in the register table
            $register = RegisterSuratKuasaModel::create([
                'surat_kuasa_id' => $pendaftaran->id,
                'uuid' => Str::uuid(),
                'tanggal_register' => Carbon::now()->toDateString(),
                'nomor_surat_kuasa' => $validated['nomor_surat_kuasa'],
                'approval_id' => Auth::id(),
                'panitera_id' => $validated['panitera_id'],
                'path_file' => '',
            ]);

            // Define new data for audit trail
            $newData = [
                'tahapan' => TahapanSuratKuasaEnum::Verifikasi->value,
                'status' => StatusSuratKuasaEnum::Disetujui->value,
                'keterangan' => 'Pendaftaran surat kuasa telah disetujui dan diregistrasi.',
                'nomor_surat_kuasa' => $validated['nomor_surat_kuasa'],
                'panitera_id' => $validated['panitera_id'],
            ];

            // Update the status of the power of attorney
            $pendaftaran->update([
                'tahapan' => $newData['tahapan'],
                'status' => $newData['status'],
                'keterangan' => $newData['keterangan']
            ]);

            // Commit the transaction first before generating PDF
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Log the error
            Log::error('Failed approve power of attorney (transaction): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Return the error response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server saat menyimpan data.'], 500);
        }

        // Post-commit operations (generate PDF, send notifications)
        // These are done after commit to ensure the database records are available
        try {
            // Dispatch jobs synchronously to generate PDFs so that the file path is immediately available.
            GenerateBarcodeSuratKuasaPDF::dispatchSync($register);

            // Load register model to get new path_file.
            $register->refresh();

            // Send email notification to user with attached PDF.
            // Email will be processed in the background because Mailable implements ShouldQueue.
            Mail::to($pendaftaran->user->email)->queue(new ApproveSuratKuasaMail($pendaftaran, $register->path_file));

            // Send notification to user
            $title = 'Pendaftaran Disetujui';
            $message = "Pendaftaran surat kuasa {$pendaftaran->id_daftar} telah disetujui.";
            $pendaftaran->user->notify(new SuratKuasaStatusNotification($pendaftaran, $title, $message));
        } catch (\Exception $postEx) {
            // Log the error but don't fail the request since the main transaction was successful
            Log::error('Failed post-approve operations (PDF/notification): ' . $postEx->getMessage(), ['trace' => $postEx->getTraceAsString()]);
            // Continue with remaining operations
        }

        // Record audit trail
        $context = [
            'old' => $oldData,
            'new' => $newData,
        ];
        AuditTrailService::record('telah menyetujui pendaftaran surat kuasa ' . $pendaftaran->id_daftar, $context);

        // Invalidate cache chart to get new data
        $cacheKey = "chart_data_" . Carbon::now()->year;
        Cache::forget($cacheKey);

        // Log the action
        Log::info('Power of attorney approved and registered: ', ['id' => $pendaftaran->id, 'nomor' => $validated['nomor_surat_kuasa']]);
        return response()->json(['success' => true, 'message' => 'Surat kuasa berhasil disetujui dan diregistrasi.']);
    }

    /**
     * Reject a power of attorney registration.
     *
     * @param RejectSuratKuasaRequest $request
     * @return JsonResponse
     */
    public function reject(RejectSuratKuasaRequest $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validated();

        // Start a database transaction
        DB::beginTransaction();
        try {
            // Decrypt the id
            $id = Crypt::decrypt($validated['id']);
            // Fetch the power of attorney
            $pendaftaran = PendaftaranSuratKuasaModel::with('user')->findOrFail($id);

            // Capture old data for audit trail
            $oldData = $pendaftaran->only(['tahapan', 'status', 'keterangan']);

            $newData = [
                'tahapan' => $validated['tahapan'],
                'status' => StatusSuratKuasaEnum::Ditolak->value,
                'keterangan' => $validated['keterangan'],
            ];

            // Update the status of the power of attorney
            $pendaftaran->update([
                'tahapan' => $newData['tahapan'],
                'status' => $newData['status'],
                'keterangan' => $newData['keterangan'],
            ]);

            // Kirim email notifikasi penolakan ke pengguna.
            // Email akan diproses di background karena Mailable mengimplementasikan ShouldQueue.
            Mail::to($pendaftaran->user->email)->queue(new RejectSuratKuasaMail($pendaftaran, $validated['keterangan']));

            // Send notification to user
            $title = 'Pendaftaran Ditolak';
            $message = "Pendaftaran surat kuasa {$pendaftaran->id_daftar} ditolak. Silakan periksa detailnya.";
            $pendaftaran->user->notify(new SuratKuasaStatusNotification($pendaftaran, $title, $message));

            // Commit the transaction
            DB::commit();

            // Record audit trail
            $context = [
                'old' => $oldData,
                'new' => $newData,
            ];
            AuditTrailService::record('telah menolak pendaftaran surat kuasa ' . $pendaftaran->id_daftar, $context);

            // Log the action
            Log::info('Power of attorney rejected: ', ['id' => $pendaftaran->id, 'alasan' => $validated['keterangan']]);
            // Return success response
            return response()->json(['success' => true, 'message' => 'Surat kuasa berhasil ditolak.']);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Log the error
            Log::error('Failed reject power of attorney: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Return error response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
