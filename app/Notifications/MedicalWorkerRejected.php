<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalWorkerRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $medicalWorker;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($medicalWorker, $reason = null)
    {
        $this->medicalWorker = $medicalWorker;
        $this->reason = $reason;
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
            ->subject('MediConnect Application Status Update')
            ->greeting('Hello ' . $this->medicalWorker->name . ',')
            ->line('We have completed the review of your application to join MediConnect as a medical worker.')
            ->line('After careful consideration, we regret to inform you that your application has not been approved at this time.');

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        return $message->line('You may reapply after 30 days with updated documentation and information.')
            ->line('If you believe this decision was made in error or would like more information, please contact our support team.')
            ->action('Contact Support', url('/contact-us'))
            ->line('We appreciate your interest in MediConnect and wish you success in your medical career.');
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
            'status' => 'rejected',
            'reason' => $this->reason,
            'message' => 'Medical worker application rejected',
        ];
    }
}
