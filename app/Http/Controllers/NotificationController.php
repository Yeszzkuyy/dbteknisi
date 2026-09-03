<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function status(Request $request)
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications()->count(),
            'unassigned' => Lead::whereNull('assigned_to')->count(),
        ]);
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}