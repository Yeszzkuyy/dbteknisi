<?php

namespace App\Http\Controllers;

use App\Ai\Agents\OfficeAssistant;
use App\Support\KnowledgeBaseCitations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OfficeAssistantController extends Controller
{
    public function index(Request $request)
    {
        $conversation = $request->user()->conversations()
            ->with(['messages' => fn ($query) => $query->orderBy('created_at')])
            ->latest('updated_at')
            ->first();

        $messages = $conversation?->messages
            ->map(fn ($message) => [
                'id' => $message->id,
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->values()
            ->all() ?? [];

        return view('ai.chat', compact('conversation', 'messages'));
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'conversation_id' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        if (blank(config('ai.providers.openrouter.key'))) {
            return response()->json([
                'message' => 'Asisten AI belum aktif karena API key OpenRouter belum dikonfigurasi. Hubungi administrator.',
            ], 422);
        }

        $conversation = null;
        if (! blank($validated['conversation_id'] ?? null)) {
            $conversation = $user->conversations()->find($validated['conversation_id']);

            if (! $conversation) {
                return response()->json([
                    'message' => 'Percakapan tidak valid. Silakan mulai percakapan baru.',
                ], 422);
            }
        }

        try {
            $agent = new OfficeAssistant;

            $response = $conversation
                ? $agent->continue($conversation->id, as: $user)->prompt($validated['message'])
                : $agent->forUser($user)->prompt($validated['message']);

            return response()->json([
                'message' => (string) $response->text,
                'conversation_id' => $response->conversationId,
                'sources' => KnowledgeBaseCitations::resolve($response->meta?->citations),
            ]);
        } catch (Throwable $e) {
            Log::error('OfficeAssistant gagal merespons: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Maaf, asisten tidak dapat merespons saat ini. Coba lagi beberapa saat.',
            ], 500);
        }
    }
}
