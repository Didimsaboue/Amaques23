<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Invitation;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $existingActiveInvitation = Invitation::where('statue', true)->first();

        if ($existingActiveInvitation && $existingActiveInvitation->date_fin < now()) {
            $notifications = Notification::whereNull('read_at')->get();
            return response()->json($notifications);
        }

        return response()->json([]);
    }

    public function markAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['message' => 'Notifications marquées comme lues.']);
    }

    public function pastNotifications()
    {
        $notifications = Notification::whereNotNull('read_at')->get();
        return response()->json($notifications);
    }
}
