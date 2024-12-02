<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'date_fin',
        'statue',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'statue' => 'boolean',
    ];

    public function isFinishedAndActive()
    {
        $isFinished = $this->statue && Carbon::now()->gt(Carbon::parse($this->date_fin));
        Log::info("Checking if campaign {$this->nom} is finished and active: " . ($isFinished ? 'Yes' : 'No'));
        return $isFinished;
    }

    public function createNotification()
    {
        if ($this->isFinishedAndActive()) {
            \App\Models\Notification::create([
                'title' => 'Campagne terminée',
                'message' => "La date de la campagne '{$this->nom}' est terminée",
            ]);
            Log::info("Notification créée pour la campagne : {$this->nom}");

            $this->update(['statue' => false]);

            $this->updateNotificationCount();
        }
    }

    public function updateNotificationCount()
    {
        $notificationCount = \App\Models\Notification::whereNull('read_at')->count();
        broadcast(new \App\Events\NotificationsCountUpdated($notificationCount))->toOthers();
    }
}
