<?php

namespace App\Http\Controllers\Suratkuasa;

use Carbon\Carbon;
use App\Mail\ApproveSuratKuasaMail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enum\StatusSuratKuasaEnum;
use App\Enum\TahapanSuratKuasaEnum;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RejectSuratKuasaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
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

            // Check if the power of attorney has already been registered
            if ($pendaftaran->register) {
                return response()->json(['success' => false, 'message' => 'Surat kuasa ini sudah diregistrasi sebelumnya.'], 409);
            }

            // Check if the number of the power of attorney has already been used in the current year
            $currentYear = Carbon::now()->year;
            $isDuplicate = RegisterSuratKuasaModel::where('nomor_surat_kuasa', $validated['nomor_surat_kuasa'])
                ->whereYear('created_at', $currentYear)
                ->exists();

            if ($isDuplicate) {
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

            // Update the status of the power of attorney
            $pendaftaran->update([
                'tahapan' => TahapanSuratKuasaEnum::Verifikasi->value,
                'status' => StatusSuratKuasaEnum::Disetujui->value,
                'keterangan' => 'Pendaftaran surat kuasa telah disetujui dan diregistrasi.'
            ]);

            // Dispatch job secara sinkron untuk generate PDF agar path file langsung tersedia.
            GenerateBarcodeSuratKuasaPDF::dispatchSync($register);

            // Muat ulang model register untuk mendapatkan path_file yang baru.
            $register->refresh();

            // Kirim email notifikasi ke pengguna dengan lampiran PDF.
            // Email akan diproses di background karena Mailable mengimplementasikan ShouldQueue.
            Mail::to($pendaftaran->user->email)->queue(new ApproveSuratKuasaMail($pendaftaran, $register->path_file));

            // Commit the transaction
            DB::commit();


            // Log the action
            Log::info('Power of attorney approved and registered: ', ['id' => $pendaftaran->id, 'nomor' => $validated['nomor_surat_kuasa']]);
            return response()->json(['success' => true, 'message' => 'Surat kuasa berhasil disetujui dan diregistrasi.']);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Log the error
            Log::error('Failde approve power of attorney: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Return the error response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
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

            // Update the status of the power of attorney
            $pendaftaran->update([
                'tahapan' => $validated['tahapan'],
                'status' => StatusSuratKuasaEnum::Ditolak->value,
                'keterangan' => $validated['keterangan'],
            ]);

            // Kirim email notifikasi penolakan ke pengguna.
            // Email akan diproses di background karena Mailable mengimplementasikan ShouldQueue.
            Mail::to($pendaftaran->user->email)->queue(new RejectSuratKuasaMail($pendaftaran, $validated['keterangan']));

            // Commit the transaction
            DB::commit();

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
