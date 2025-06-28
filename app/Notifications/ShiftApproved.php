<?php

namespace App\Notifications;

use App\Models\LocumShift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ShiftApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LocumShift $shift) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'shift_id' => $this->shift->id,
            'title'    => $this->shift->title,
            'facility' => optional($this->shift->facility)->facility_name ?? '',
            'start'    => $this->shift->start_datetime,
        ];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage($this->toArray($notifiable));
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
