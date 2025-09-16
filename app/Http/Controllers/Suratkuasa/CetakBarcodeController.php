<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\GenerateBarcodeSuratKuasaPDF;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CetakBarcodeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
            $pendaftaran = PendaftaranSuratKuasaModel::with('register')->findOrFail($id);

            // If the registration has not been approved, redirect back with an error message.
            if (!$pendaftaran->register) {
                Log::warning('Attempt to download barcode for unapproved registration.', ['pendaftaran_id' => $id]);
                return redirect()->back()->with('error', 'File barcode belum tersedia atau pendaftaran belum disetujui.');
            }

            return $this->checkAndGeneratePdf($pendaftaran->register);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Error decrypting barcode download ID: ' . $e->getMessage());
            return redirect()->route('surat-kuasa.index')->with('error', 'ID Pendaftaran tidak valid.');
        } catch (\Exception $e) {
            // Log the error and return a generic error message.
            Log::error('Error generating or downloading barcode: ' . $e->getMessage(), ['pendaftaran_id' => $id ?? null]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Check if the PDF file exists and generate it if not.
     *
     * @param RegisterSuratKuasaModel $register
     * @return StreamedResponse
     * @throws \Exception
     */
    private function checkAndGeneratePdf(RegisterSuratKuasaModel $register): StreamedResponse
    {
        $filePath = $register->path_file;

        // Condition: The file path is empty or the file does not exist in storage, then regenerate.
        if (empty($filePath) || !Storage::disk('local')->exists($filePath)) {
            Log::warning('File barcode PDF tidak ditemukan, mencoba membuat ulang.', [
                'register_id' => $register->id,
                'path' => $filePath ?? 'Path kosong'
            ]);

            // Run jobs synchronously to create PDFs directly.
            GenerateBarcodeSuratKuasaPDF::dispatchSync($register);

            // Refresh the model to get the new path.
            $register->refresh();
            $newFilePath = $register->path_file;

            // Condition: The new file path is empty or the file does not exist in storage, then throw an exception.
            if (empty($newFilePath) || !Storage::disk('local')->exists($newFilePath)) {
                Log::error('Failed to recreate and find PDF barcode file.', ['register_id' => $register->id, 'path' => $newFilePath]);
                // Lemparkan exception untuk ditangkap oleh method index.
                throw new \Exception('Gagal membuat ulang file barcode. Silakan hubungi administrator.');
            }

            // If successful, download the newly created file.
            return Storage::disk('local')->download($newFilePath);
        }
        return Storage::disk('local')->download($filePath);
    }
}
