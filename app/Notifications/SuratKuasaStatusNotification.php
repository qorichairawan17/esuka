<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class SuratKuasaStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pendaftaran;
    protected $title;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(PendaftaranSuratKuasaModel $pendaftaran, string $title, string $message)
    {
        $this->pendaftaran = $pendaftaran;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pendaftaran_id' => $this->pendaftaran->id,
            'id_daftar' => $this->pendaftaran->id_daftar,
            'title' => $this->title,
            'message' => $this->message,
            'url' => route('surat-kuasa.detail', ['id' => Crypt::encrypt($this->pendaftaran->id)]),
        ];
    }
}
