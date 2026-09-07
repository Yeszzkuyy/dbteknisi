<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function status(Request $request)
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications()->count(),
            'unassigned' => Lead::whereNull('assigned_to')->count(),
            'items' => self::itemsFor($request->user()),
        ]);
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    public static function itemsFor(User $user): array
    {
        return $user->notifications()->limit(10)->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'url' => route('manage-sales.edit', $n->data['lead_id'] ?? 0),
                'customer' => $n->data['customer'] ?? 'Lead baru',
                'read' => (bool) $n->read_at,
                'ago' => $n->created_at->diffForHumans(),
            ])
            ->values()
            ->all();
    }
}