<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LocumShift;

class NewShiftAvailable extends Notification
{
    use Queueable;

    protected $shift;

    /**
     * Create a new notification instance.
     */
    public function __construct(LocumShift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Shift Available - ' . $this->shift->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new shift opportunity is available that matches your specialty.')
            ->line('**Shift Details:**')
            ->line('Title: ' . $this->shift->title)
            ->line('Location: ' . $this->shift->location)
            ->line('Start: ' . $this->shift->start_datetime->format('M j, Y g:i A'))
            ->line('End: ' . $this->shift->end_datetime->format('M j, Y g:i A'))
            ->line('Pay Rate: $' . number_format($this->shift->pay_rate, 2) . '/hour')
            ->line('Available Slots: ' . $this->shift->slots_available)
            ->action('View Shift Details', url('/api/worker/shifts/' . $this->shift->id))
            ->line('Apply quickly as slots are limited!')
            ->line('Thank you for using MediConnect!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'shift_id' => $this->shift->id,
            'title' => $this->shift->title,
            'facility_name' => $this->shift->facility->facility_name,
            'location' => $this->shift->location,
            'start_datetime' => $this->shift->start_datetime,
            'end_datetime' => $this->shift->end_datetime,
            'pay_rate' => $this->shift->pay_rate,
            'slots_available' => $this->shift->slots_available,
            'worker_type' => $this->shift->worker_type,
            'message' => 'New shift available: ' . $this->shift->title,
            'type' => 'new_shift'
        ];
    }
}
