<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;
use Carbon\Carbon;

class InvitEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $subject;
    public $currentDateTime;

    public function __construct(Invitation $invitation, $subject)
    {
        $this->invitation = $invitation;
        $this->subject = $subject;
        $this->currentDateTime = Carbon::now()->format('d/m/Y H:i');
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('dashadmin.mail');
    }
}
