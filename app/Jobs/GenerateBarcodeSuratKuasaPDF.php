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
            $infoApp = AplikasiModel::first(); //Get application setting

            // Generate URL QR Code base on UUID
            $qrCodeUrl = route('app.surat-kuasa.verify', ['uuid' => $register->uuid]);
            // Generate QR Code as base64 string for embedding in PDF
            $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate($qrCodeUrl));

            // Prepare data for view
            $data = [
                'title' => 'Bukti Pendaftaran Surat Kuasa - ' . $pendaftaran->id_daftar,
                'register' => $register,
                'infoApp' => $infoApp, // Kirim data aplikasi ke view
                'pendaftaran' => $pendaftaran,
                'qrCode' => $qrCode,
                'qrCodeUrl' => $qrCodeUrl,
            ];

            // Generate PDF from view
            $pdf = Pdf::loadView('admin.template.pdf-barcode', $data);

            // Generate file name
            $fileName = 'barcode-surat-kuasa-' . str_replace('#', '', $pendaftaran->id_daftar) . '.pdf';
            $filePath = 'barcode/' . date('Y/m') . '/' . $fileName;

            // Save PDF to storage
            Storage::disk('local')->put($filePath, $pdf->output());

            // Update path_file di database
            $register->update(['path_file' => $filePath]);

            Log::info('Berhasil generate PDF barcode untuk surat kuasa.', ['register_id' => $register->id, 'path' => $filePath]);
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF barcode.', [
                'register_id' => $this->register->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
