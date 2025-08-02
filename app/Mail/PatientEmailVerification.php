<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $verificationToken;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient, string $verificationToken)
    {
        $this->patient = $patient;
        $this->verificationToken = $verificationToken;
        // Use Laravel backend API for verification instead of frontend URL
        $this->verificationUrl = config('app.url', 'http://127.0.0.1:8000') . '/api/auth/verify-email?token=' . $verificationToken;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MediConnect - Verify Your Email Address',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.patient-verification',
            with: [
                'patient' => $this->patient,
                'verificationUrl' => $this->verificationUrl,
                'verificationToken' => $this->verificationToken,
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
