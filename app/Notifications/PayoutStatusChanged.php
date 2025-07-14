<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PayoutRequest $payout) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payout Request '.$this->payout->status)
            ->line('Your payout request of '.$this->payout->amount.' has been '.$this->payout->status.'.')
            ->action('View Wallet', url('/worker/wallet'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payout_id' => $this->payout->id,
            'status'    => $this->payout->status,
            'amount'    => $this->payout->amount,
        ];
    }
}
