<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivateAccountMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $title;
    public $user;
    public $activationUrl;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(String $title, User $user, String $activationUrl, $newPassword = null)
    {
        $this->title = $title;
        $this->user = $user;
        $this->activationUrl = $activationUrl;
        $this->newPassword = $newPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title . ' ' .  config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.aktivasi-akun',
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
