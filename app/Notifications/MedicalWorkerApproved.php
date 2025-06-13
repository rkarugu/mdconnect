<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalWorkerApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $medicalWorker;
    protected $password;
    protected $appDownloadLink;

    /**
     * Create a new notification instance.
     */
    public function __construct($medicalWorker, $password, $appDownloadLink = null)
    {
        $this->medicalWorker = $medicalWorker;
        $this->password = $password;
        $this->appDownloadLink = $appDownloadLink ?? config('app.medical_worker_app_download_url', 'https://mediconnect.com/download/medical-worker-app');
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
            ->subject('Congratulations! Your MediConnect Medical Worker Application is Approved')
            ->greeting('Hello ' . $this->medicalWorker->name . '!')
            ->line('Great news! Your application to become a Medical Worker on MediConnect has been approved.')
            ->line('We are excited to welcome you to our network of healthcare professionals.')
            ->line('You can now download the MediConnect Medical Worker app and log in with the following credentials:')
            ->line('Email: ' . $this->medicalWorker->email)
            ->line('Password: ' . $this->password . ' (You will be required to change this password upon first login)')
            ->action('Download MediConnect App', $this->appDownloadLink)
            ->line('Once logged in, you will have access to the following features:')
            ->line('• Manage your professional profile')
            ->line('• View and apply for healthcare job opportunities')
            ->line('• Connect with medical facilities and patients')
            ->line('• Schedule appointments and telemedicine sessions')
            ->line('If you have any questions or need assistance, please contact our support team.')
            ->line('Welcome to the MediConnect family!');
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
            'status' => 'approved',
            'message' => 'Medical worker application approved',
            'app_download_link' => $this->appDownloadLink
        ];
    }
}
