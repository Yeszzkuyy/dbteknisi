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
            ->map(function ($n) {
                $data = $n->data;
                $isWhatsapp = ($data['type'] ?? null) === 'whatsapp';

                return [
                    'id' => $n->id,
                    'url' => $isWhatsapp
                        ? ($data['url'] ?? route('whatsapp-center.index'))
                        : route('manage-sales.edit', $data['lead_id'] ?? 0),
                    'customer' => $data['customer'] ?? 'Lead baru',
                    'preview' => $isWhatsapp ? ($data['preview'] ?? '') : null,
                    'type' => $isWhatsapp ? 'whatsapp' : 'lead',
                    'read' => (bool) $n->read_at,
                    'ago' => $n->created_at->diffForHumans(),
                ];
            })
            ->values()
            ->all();
    }
}