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
        try {
            // Reload the model from the database to ensure the latest data and relations can be loaded.
            $register = RegisterSuratKuasaModel::with(['pendaftaran.pihak', 'panitera'])->findOrFail($this->register->id);

            $pendaftaran = $register->pendaftaran;

            // Validate that pendaftaran exists
            if (!$pendaftaran) {
                throw new \Exception("Pendaftaran not found for register ID: {$register->id}");
            }

            // Validate that panitera exists
            if (!$register->panitera) {
                throw new \Exception("Panitera not found for register ID: {$register->id}");
            }

            $infoApp = AplikasiModel::first(); //Get application setting

            // Validate that infoApp exists
            if (!$infoApp) {
                throw new \Exception("Application settings not found");
            }

            // Generate URL QR Code base on UUID
            $qrCodeUrl = route('app.surat-kuasa.verify', ['uuid' => $register->uuid]);

            // Generate QR Code as base64 string for embedding in PDF
            $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate($qrCodeUrl));

            // Prepare data for view
            $data = [
                'title' => 'Bukti Pendaftaran Surat Kuasa - ' . $pendaftaran->id_daftar,
                'register' => $register,
                'infoApp' => $infoApp, // Pass application setting
                'pendaftaran' => $pendaftaran,
                'qrCode' => $qrCode,
                'qrCodeUrl' => $qrCodeUrl,
            ];

            // Generate PDF from view
            $pdf = Pdf::loadView('admin.template.pdf-barcode', $data);

            // Generate file name - add random string to prevent duplicate filename issues
            $randomSuffix = strtoupper(substr(uniqid(), -4));
            $fileName = 'barcode-surat-kuasa-' . str_replace('#', '', $pendaftaran->id_daftar) . '-' . $randomSuffix . '.pdf';
            $filePath = 'barcode/' . date('Y') . '/' . date('m') . '/' . $fileName;

            // Ensure directory exists
            $directory = dirname($filePath);
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }

            // Save PDF to storage
            $saved = Storage::disk('local')->put($filePath, $pdf->output());

            if (!$saved) {
                throw new \Exception("Failed to save PDF file to storage: {$filePath}");
            }

            // Update path_file di database
            $register->update(['path_file' => $filePath]);

            Log::info('Success generate PDF barcode.', ['register_id' => $register->id, 'path' => $filePath]);
        } catch (\Exception $e) {
            Log::error('Error generate PDF barcode.', [
                'register_id' => $this->register->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception so it can be caught by the caller when using dispatchSync
            throw $e;
        }
    }
}
