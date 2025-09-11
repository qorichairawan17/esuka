<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateBarcodeSuratKuasaPDF;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CetakBarcodeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
            $pendaftaran = PendaftaranSuratKuasaModel::with('register')->findOrFail($id);

            // Kondisi 1: Pendaftaran belum diregistrasi/disetujui.
            if (!$pendaftaran->register) {
                Log::warning('Upaya mengunduh barcode untuk pendaftaran yang belum disetujui.', ['pendaftaran_id' => $id]);
                return redirect()->back()->with('error', 'File barcode belum tersedia atau pendaftaran belum disetujui.');
            }

            return $this->checkAndGeneratePdf($pendaftaran->register);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Error decrypting barcode download ID: ' . $e->getMessage());
            return redirect()->route('surat-kuasa.index')->with('error', 'ID Pendaftaran tidak valid.');
        } catch (\Exception $e) {
            // Menangkap error dari checkAndGeneratePdf, misalnya saat regenerasi gagal.
            Log::error('Gagal memproses unduhan barcode: ' . $e->getMessage(), ['pendaftaran_id' => $id ?? null]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memeriksa keberadaan file PDF, membuat ulang jika tidak ada, dan mengembalikan response download.
     *
     * @param RegisterSuratKuasaModel $register
     * @return StreamedResponse
     * @throws \Exception
     */
    private function checkAndGeneratePdf(RegisterSuratKuasaModel $register): StreamedResponse
    {
        $filePath = $register->path_file;

        // Kondisi: Path file kosong atau file tidak ada di storage, maka generate ulang.
        if (empty($filePath) || !Storage::disk('local')->exists($filePath)) {
            Log::warning('File barcode PDF tidak ditemukan, mencoba membuat ulang.', [
                'register_id' => $register->id,
                'path' => $filePath ?? 'Path kosong'
            ]);

            // Jalankan job secara sinkron untuk membuat PDF secara langsung.
            GenerateBarcodeSuratKuasaPDF::dispatchSync($register);

            // Muat ulang model untuk mendapatkan path_file yang baru.
            $register->refresh();
            $newFilePath = $register->path_file;

            // Pemeriksaan akhir: jika file masih tidak ada, berarti ada masalah kritis.
            if (empty($newFilePath) || !Storage::disk('local')->exists($newFilePath)) {
                Log::error('Gagal membuat ulang dan menemukan file barcode PDF.', ['register_id' => $register->id, 'path' => $newFilePath]);
                // Lemparkan exception untuk ditangkap oleh method index.
                throw new \Exception('Gagal membuat ulang file barcode. Silakan hubungi administrator.');
            }

            // Jika berhasil, unduh file yang baru dibuat.
            return Storage::disk('local')->download($newFilePath);
        }

        // Jika semua baik-baik saja, kembalikan file yang ada.
        return Storage::disk('local')->download($filePath);
    }
}
