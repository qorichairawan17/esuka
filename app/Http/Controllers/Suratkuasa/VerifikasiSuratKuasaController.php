<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Enum\StatusSuratKuasaEnum;
use App\Enum\TahapanSuratKuasaEnum;
use App\Http\Controllers\Controller;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VerifikasiSuratKuasaController extends Controller
{
    /**
     * Approve a power of attorney registration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function approve(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'nomor_surat_kuasa' => 'required|string',
            'panitera_id' => 'required|exists:sk_panitera,id',
        ], [
            'nomor_surat_kuasa.required' => 'Nomor surat kuasa wajib diisi.',
            'panitera_id.required' => 'Panitera wajib dipilih.',
            'panitera_id.exists' => 'Panitera yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $id = Crypt::decrypt($request->id);
            $pendaftaran = PendaftaranSuratKuasaModel::findOrFail($id);

            if ($pendaftaran->register) {
                return response()->json(['success' => false, 'message' => 'Surat kuasa ini sudah diregistrasi sebelumnya.'], 409);
            }

            $currentYear = Carbon::now()->year;
            $isDuplicate = RegisterSuratKuasaModel::where('nomor_surat_kuasa', $request->nomor_surat_kuasa)
                ->whereYear('created_at', $currentYear)
                ->exists();

            if ($isDuplicate) {
                return response()->json(['success' => false, 'message' => 'Nomor surat kuasa sudah digunakan tahun ini. Silakan gunakan nomor lain.'], 409);
            }

            RegisterSuratKuasaModel::create([
                'surat_kuasa_id' => $pendaftaran->id,
                'uuid' => Str::uuid(),
                'tanggal_register' => Carbon::now()->toDateString(),
                'nomor_surat_kuasa' => $request->nomor_surat_kuasa,
                'approval_id' => Auth::id(),
                'panitera_id' => $request->panitera_id,
                'path_file' => 'placeholder.pdf' // TODO: Ganti dengan logika generate PDF barcode
            ]);

            $pendaftaran->update([
                'tahapan' => TahapanSuratKuasaEnum::Verifikasi->value,
                'status' => StatusSuratKuasaEnum::Disetujui->value,
                'keterangan' => 'Pendaftaran surat kuasa telah disetujui dan diregistrasi.'
            ]);

            DB::commit();
            Log::info('Surat kuasa disetujui', ['id' => $pendaftaran->id, 'nomor' => $request->nomor_surat_kuasa]);
            return response()->json(['success' => true, 'message' => 'Surat kuasa berhasil disetujui dan diregistrasi.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyetujui surat kuasa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Reject a power of attorney registration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'keterangan' => 'required|string|min:10',
        ], [
            'keterangan.required' => 'Alasan penolakan wajib diisi.',
            'keterangan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $id = Crypt::decrypt($request->id);
            $pendaftaran = PendaftaranSuratKuasaModel::findOrFail($id);

            $nextTahapan = $pendaftaran->tahapan === TahapanSuratKuasaEnum::Pembayaran->value
                ? TahapanSuratKuasaEnum::PerbaikanPembayaran->value
                : TahapanSuratKuasaEnum::PerbaikanData->value;

            $pendaftaran->update([
                'tahapan' => $nextTahapan,
                'status' => StatusSuratKuasaEnum::Ditolak->value,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            Log::info('Surat kuasa ditolak', ['id' => $pendaftaran->id, 'alasan' => $request->keterangan]);
            return response()->json(['success' => true, 'message' => 'Surat kuasa berhasil ditolak.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menolak surat kuasa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
