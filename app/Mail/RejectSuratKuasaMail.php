<?php

namespace App\Mail;

use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectSuratKuasaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pendaftaran;
    public $keterangan;

    /**
     * Create a new message instance.
     */
    public function __construct(PendaftaranSuratKuasaModel $pendaftaran, string $keterangan)
    {
        $this->pendaftaran = $pendaftaran;
        $this->keterangan = $keterangan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Surat Kuasa Anda Ditolak - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.reject-surat-kuasa',
            with: [
                'user' => $this->pendaftaran->user,
                'suratKuasa' => $this->pendaftaran,
                'keterangan' => $this->keterangan,
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
        return [];
    }
}
