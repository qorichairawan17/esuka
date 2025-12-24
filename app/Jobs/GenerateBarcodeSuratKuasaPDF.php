<?php

namespace App\Jobs;

use App\Models\Pengaturan\AplikasiModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateBarcodeSuratKuasaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected RegisterSuratKuasaModel $register;

    /**
     * Create a new job instance.
     */
    public function __construct(RegisterSuratKuasaModel $register)
    {
        $this->register = $register;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $registerId = $this->register->id ?? 'N/A';

        try {
            // Step 1: Load data from database
            $register = RegisterSuratKuasaModel::with(['pendaftaran.pihak', 'panitera'])->findOrFail($this->register->id);
            $pendaftaran = $register->pendaftaran;

            if (!$pendaftaran) {
                throw new \Exception("STEP 1 FAILED: Pendaftaran not found for register ID: {$register->id}");
            }

            if (!$register->panitera) {
                throw new \Exception("STEP 1 FAILED: Panitera not found for register ID: {$register->id}");
            }

            $infoApp = AplikasiModel::first();
            if (!$infoApp) {
                throw new \Exception("STEP 1 FAILED: Application settings not found");
            }

            // Step 2: Generate QR Code
            try {
                $qrCodeUrl = route('app.surat-kuasa.verify', ['uuid' => $register->uuid]);
                $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate($qrCodeUrl));
            } catch (\Exception $qrEx) {
                throw new \Exception("STEP 2 FAILED (QR Code): " . $qrEx->getMessage());
            }

            // Step 3: Generate PDF
            try {
                $data = [
                    'title' => 'Bukti Pendaftaran Surat Kuasa - ' . $pendaftaran->id_daftar,
                    'register' => $register,
                    'infoApp' => $infoApp,
                    'pendaftaran' => $pendaftaran,
                    'qrCode' => $qrCode,
                    'qrCodeUrl' => $qrCodeUrl,
                ];

                $pdf = Pdf::loadView('admin.template.pdf-barcode', $data);
                $pdfOutput = $pdf->output();

                if (empty($pdfOutput)) {
                    throw new \Exception("PDF output is empty");
                }
            } catch (\Exception $pdfEx) {
                throw new \Exception("STEP 3 FAILED (PDF Generation): " . $pdfEx->getMessage());
            }

            // Step 4: Save to storage
            try {
                $randomSuffix = strtoupper(substr(uniqid(), -4));
                $fileName = 'barcode-surat-kuasa-' . str_replace('#', '', $pendaftaran->id_daftar) . '-' . $randomSuffix . '.pdf';
                $filePath = 'barcode/' . date('Y') . '/' . date('m') . '/' . $fileName;

                $directory = dirname($filePath);
                if (!Storage::disk('local')->exists($directory)) {
                    Storage::disk('local')->makeDirectory($directory);
                }

                $saved = Storage::disk('local')->put($filePath, $pdfOutput);

                if (!$saved) {
                    $storagePath = storage_path('app/' . $directory);
                    $isWritable = is_writable(storage_path('app'));
                    throw new \Exception("Storage::put returned false. Path: {$filePath}, Storage writable: " . ($isWritable ? 'YES' : 'NO'));
                }

                // Verify file exists
                if (!Storage::disk('local')->exists($filePath)) {
                    throw new \Exception("File not found after save: {$filePath}");
                }
            } catch (\Exception $storageEx) {
                throw new \Exception("STEP 4 FAILED (Storage): " . $storageEx->getMessage());
            }

            // Step 5: Update database
            $register->update(['path_file' => $filePath]);

            Log::info('Success generate PDF barcode.', ['register_id' => $register->id, 'path' => $filePath]);
        } catch (\Exception $e) {
            Log::error('BARCODE_PDF_ERROR', [
                'register_id' => $registerId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Re-throw the exception
            throw $e;
        }
    }
}
