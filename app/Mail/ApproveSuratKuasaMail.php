<?php

namespace App\Mail;

use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApproveSuratKuasaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pendaftaran;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(PendaftaranSuratKuasaModel $pendaftaran, string $pdfPath)
    {
        $this->pendaftaran = $pendaftaran;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Surat Kuasa Anda Telah Disetujui - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.approve-surat-kuasa',
            with: [
                'user' => $this->pendaftaran->user,
                'suratKuasa' => $this->pendaftaran,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        try {
            $fullPath = Storage::disk('local')->path($this->pdfPath);

            // Check if the file exists before trying to attach it.
            if (!Storage::disk('local')->exists($this->pdfPath)) {
                Log::error('File PDF tidak ditemukan untuk lampiran di ApproveSuratKuasaMail.', [
                    'pendaftaran_id' => $this->pendaftaran->id,
                    'path' => $this->pdfPath,
                    'full_path_attempted' => $fullPath,
                ]);
                return []; // return empty array
            }

            Log::info('Attaching PDF to ApprovePower of Attorney Mail.', [
                'pendaftaran_id' => $this->pendaftaran->id,
                'path' => $this->pdfPath,
            ]);

            return [
                Attachment::fromPath($fullPath)
                    ->as(basename($this->pdfPath)) // Use the file name as the attachment name
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to attach PDF in ApproveSuratKuasaMail.', [
                'pendaftaran_id' => $this->pendaftaran->id,
                'path' => $this->pdfPath,
                'error' => $e->getMessage(),
            ]);
            return []; // return empty array
        }
    }
}
