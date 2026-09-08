<?php

namespace App\Http\Controllers;

use App\Ai\Agents\OfficeAssistant;
use App\Rules\SecureFile;
use App\Support\KnowledgeBaseCitations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Files\Image;
use Laravel\Ai\Models\Conversation;
use Throwable;

class OfficeAssistantController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $newChat = $request->boolean('new');
        $conversationId = $request->query('conversation_id');

        $conversation = null;
        if (! $newChat && filled($conversationId)) {
            $conversation = $user->conversations()->findOrFail($conversationId);
        } elseif (! $newChat) {
            $conversation = $user->conversations()->latest('updated_at')->first();
        }

        $conversations = $user->conversations()
            ->latest('updated_at')
            ->get(['id', 'title', 'updated_at'])
            ->map(fn (Conversation $item) => $this->conversationPayload($item))
            ->values()
            ->all();

        return view('ai.chat', [
            'conversation' => $conversation,
            'conversations' => $conversations,
            'messages' => $this->messagePayload($conversation),
        ]);
    }

    public function showConversation(Request $request, string $conversation): JsonResponse
    {
        $conversation = $request->user()->conversations()->findOrFail($conversation);

        return response()->json([
            'conversation' => $this->conversationPayload($conversation),
            'messages' => $this->messagePayload($conversation),
        ]);
    }

    public function renameConversation(Request $request, string $conversation): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
        ]);

        $conversation = $request->user()->conversations()->findOrFail($conversation);
        $conversation->update(['title' => trim($validated['title'])]);

        return response()->json([
            'conversation' => $this->conversationPayload($conversation->refresh()),
        ]);
    }

    public function destroyConversation(Request $request, string $conversation): JsonResponse
    {
        $conversation = $request->user()->conversations()->findOrFail($conversation);

        DB::transaction(function () use ($conversation): void {
            $conversation->messages()->get()->each(
                fn ($message) => $this->deleteStoredAttachments($message->attachments ?? [])
            );
            $conversation->messages()->delete();
            $conversation->delete();
        });

        return response()->json(['deleted' => true]);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000', 'required_without:attachments'],
            'conversation_id' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,pdf,txt,csv,doc,docx,xls,xlsx,ppt,pptx',
                SecureFile::documents(),
            ],
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

        $message = trim((string) ($validated['message'] ?? ''));
        $prompt = $message !== '' ? $message : 'Tolong analisis lampiran ini.';
        $storedPaths = [];

        try {
            $attachments = $this->storeAttachments($request, $storedPaths);
            $agent = new OfficeAssistant;

            $response = $conversation
                ? $agent->continue($conversation->id, as: $user)->prompt($prompt, $attachments)
                : $agent->forUser($user)->prompt($prompt, $attachments);

            $conversation = $user->conversations()->findOrFail($response->conversationId);

            return response()->json([
                'message' => (string) $response->text,
                'conversation_id' => $response->conversationId,
                'conversation' => $this->conversationPayload($conversation),
                'sources' => KnowledgeBaseCitations::resolve($response->meta?->citations),
            ]);
        } catch (Throwable $e) {
            $this->deleteStoredAttachments($storedPaths);
            Log::error('OfficeAssistant gagal merespons: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Maaf, asisten tidak dapat merespons saat ini. Coba lagi beberapa saat.',
            ], 500);
        }
    }

    /**
     * @param  array<int, string>  $storedPaths
     * @return array<int, object>
     */
    protected function storeAttachments(Request $request, array &$storedPaths): array
    {
        return collect($request->file('attachments', []))
            ->map(function (UploadedFile $file) use ($request, &$storedPaths) {
                $name = SecureFile::sanitizeName($file->getClientOriginalName());
                $path = $file->storeAs(
                    'ai-attachments/'.$request->user()->id,
                    Str::uuid()->toString().'-'.$name,
                    'local',
                );

                if (! is_string($path)) {
                    throw new \RuntimeException('Lampiran gagal disimpan.');
                }

                $storedPaths[] = $path;
                $mime = $file->getMimeType() ?: $file->getClientMimeType();

                return str_starts_with($mime, 'image/')
                    ? Image::fromStorage($path, 'local')->as($name)->withMimeType($mime)
                    : Document::fromStorage($path, 'local')->as($name)->withMimeType($mime);
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function messagePayload(?Conversation $conversation): array
    {
        if (! $conversation) {
            return [];
        }

        return $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn ($message) => [
                'id' => $message->id,
                'role' => $message->role,
                'content' => $message->content,
                'sources' => $message->role === 'assistant'
                    ? KnowledgeBaseCitations::resolve(($message->meta ?? [])['citations'] ?? [])
                    : [],
                'attachments' => $message->role === 'user'
                    ? collect($message->attachments ?? [])->map(fn ($attachment) => [
                        'name' => $attachment['name'] ?? 'Lampiran',
                        'type' => $attachment['type'] ?? 'file',
                        'mime' => $attachment['mime'] ?? null,
                    ])->values()->all()
                    : [],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function conversationPayload(Conversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'title' => $conversation->title ?: 'Percakapan baru',
            'updated_at' => $conversation->updated_at?->toISOString(),
        ];
    }

    /**
     * Delete only files created by this chat feature.
     *
     * @param  array<int, mixed>  $attachments
     */
    protected function deleteStoredAttachments(array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (! is_array($attachment) || ! str_starts_with((string) ($attachment['type'] ?? ''), 'stored-')) {
                continue;
            }

            $path = (string) ($attachment['path'] ?? '');
            if (! Str::startsWith($path, 'ai-attachments/')) {
                continue;
            }

            Storage::disk($attachment['disk'] ?? 'local')->delete($path);
        }

        foreach ($attachments as $path) {
            if (is_string($path) && Str::startsWith($path, 'ai-attachments/')) {
                Storage::disk('local')->delete($path);
            }
        }
    }
}
