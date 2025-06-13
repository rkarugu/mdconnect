<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalWorkerRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $medicalWorker;

    /**
     * Create a new notification instance.
     */
    public function __construct($medicalWorker)
    {
        $this->medicalWorker = $medicalWorker;
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
        return (new MailMessage)
            ->subject('Medical Worker Application Received - MediConnect')
            ->greeting('Hello ' . $this->medicalWorker->name . '!')
            ->line('Thank you for applying to join MediConnect as a medical worker.')
            ->line('Your application has been received and is currently under review by our team.')
            ->line('The verification process typically takes 1-3 business days and includes:')
            ->line('1. Document verification - We will review all submitted documents for authenticity')
            ->line('2. Credential verification - We will verify your professional license and qualifications')
            ->line('3. Background check - A standard procedure for all healthcare professionals')
            ->line('Once your application is approved, you will receive another email with your login credentials and instructions to download the MediConnect medical worker app.')
            ->line('If you have any questions during this process, please contact our support team.')
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
            'name' => $this->medicalWorker->name,
            'email' => $this->medicalWorker->email,
            'status' => 'pending',
            'message' => 'Medical worker application received and pending review',
        ];
    }
}
