<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalWorkerDocumentVerified extends Notification implements ShouldQueue
{
    use Queueable;

    protected $medicalWorker;
    protected $document;
    protected $isVerified;

    /**
     * Create a new notification instance.
     */
    public function __construct($medicalWorker, $document, $isVerified = true)
    {
        $this->medicalWorker = $medicalWorker;
        $this->document = $document;
        $this->isVerified = $isVerified;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->isVerified ? 
                'Document Verified - MediConnect' : 
                'Document Verification Failed - MediConnect')
            ->greeting('Hello ' . $this->medicalWorker->name . '!');

        if ($this->isVerified) {
            $message->line('Your document "' . $this->document->title . '" has been successfully verified.')
                ->line('This is an important step toward completing your application process.')
                ->line('You will be notified once all your documents have been verified and your application has been fully approved.');
        } else {
            $message->line('We regret to inform you that your document "' . $this->document->title . '" could not be verified.')
                ->line('Reason: ' . ($this->document->status_reason ?? 'The document did not meet our verification standards.'))
                ->line('Please resubmit this document with the correct information through your account portal.')
                ->line('Your application process is on hold until all required documents are verified.');
        }

        return $message->line('If you have any questions, please contact our support team.')
            ->action('View Application Status', url('/medical-workers/status'))
            ->line('Thank you for choosing MediConnect as your healthcare platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'medical_worker_id' => $this->medicalWorker->id,
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'is_verified' => $this->isVerified,
            'status_reason' => $this->document->status_reason ?? null,
        ];
    }
}
