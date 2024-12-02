<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class CheckCampaignEndDates extends Command
{
    protected $signature = 'invitations:check';
    protected $description = 'Check for finished invitations and create notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $invitations = Invitation::where('statue', true)->get();
        Log::info('Running invitations:check command. Total active invitations: ' . $invitations->count());

        foreach ($invitations as $invitation) {
            Log::info("Processing invitation: {$invitation->nom}");
            $invitation->createNotification();
        }

        return Command::SUCCESS;
    }
}
