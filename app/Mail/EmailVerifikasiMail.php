<?php

namespace App\Mail;

use App\Models\Pengguna;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerifikasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pengguna $pengguna,
        public readonly string $token
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email Anda — TELLINTER',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verifikasi',
            with: [
                'pengguna' => $this->pengguna,
                'verificationUrl' => route('verification.verify', $this->token),
            ]
        );
    }
}
