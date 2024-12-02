<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification
{
    use Queueable;

    protected $campaign;

    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Campagne expirée',
            'message' => 'La campagne "'.$this->campaign->nom.'" est terminée.',
        ];
    }
}
