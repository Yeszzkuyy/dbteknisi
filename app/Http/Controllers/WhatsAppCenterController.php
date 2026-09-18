<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappConversation;
use App\Models\WhatsappConversationPreference;
use App\Models\WhatsappMessage;
use App\Notifications\NewLeadNotification;
use App\Notifications\WhatsappInboundNotification;
use App\Services\WhatsappBot;
use App\Services\WhatsappGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class WhatsAppCenterController extends Controller
{
    public function __construct(
        private readonly WhatsappGateway $gateway,
        private readonly WhatsappBot $bot,
    ) {}

    public function index()
    {
        $accounts = $this->visibleAccounts()->get();
        $accountTabs = $accounts->map(fn ($a) => [
            'id' => $a->id,
            'account_code' => $a->account_code,
            'label' => $a->name ?: 'WA '.strtoupper(substr($a->account_code, 3)),
            'name' => $a->name,
            'phone_number' => $a->phone_number,
            'gateway_status' => $a->gateway_status,
        ])->values();

        return view('whatsapp-center.index', compact('accounts', 'accountTabs'));
    }

    public function status()
    {
        $data = $this->visibleAccounts()->get()->map(function ($account) {
            return [
                'id' => $account->id,
                'account_code' => $account->account_code,
                'unread' => $this->unreadCount($account),
                'gateway_status' => $account->gateway_status,
            ];
        });

        return response()->json($data);
    }

    public function checkStatus(WhatsappAccount $account)
    {
        $this->authorizeAccount($account);

        // Cek status manual (satu kali), khusus untuk akun Meta agar tidak
        // memicu rate limit lewat polling otomatis tiap 5 detik.
        if (! $this->gateway->isMeta($account)) {
            return response()->json([
                'gateway_status' => $account->gateway_status,
            ]);
        }

        $state = $this->gateway->getState($account);
        $status = $state;

        if ($status !== $account->gateway_status) {
            $account->update(['gateway_status' => $status]);
        }

        return response()->json(['gateway_status' => $status]);
    }

    public function conversations(WhatsappAccount $account)
    {
        $this->authorizeAccount($account);

        $preferences = WhatsappConversationPreference::where('user_id', auth()->id())
            ->where('whatsapp_account_id', $account->id)
            ->get()
            ->keyBy('sender_number');

        $conversations = WhatsappConversation::where('whatsapp_account_id', $account->id)
            ->with('handler:id,name')
            ->get()
            ->keyBy('sender_number');

        $rows = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->orderBy('id')
            ->get()
            ->groupBy('sender_number')
            ->map(function ($messages, $sender) use ($preferences, $conversations) {
                $last = $messages->last();
                $lastOutboundId = $messages->where('direction', 'outbound')->last()?->id ?? 0;
                $lead = $messages->whereNotNull('lead_id')->first()?->lead;
                $preference = $preferences->get($sender);
                $conversation = $conversations->get((string) $sender);

                return [
                    'sender_number' => (string) $sender,
                    'sender_name' => $last->sender_name ?? 'Kontak WA',
                    'last_message' => $last->message_body,
                    'last_direction' => $last->direction,
                    'last_at' => $last->created_at->toIso8601String(),
                    'unread' => $messages->where('direction', 'inbound')->where('id', '>', $lastOutboundId)->whereNull('read_at')->count(),
                    'customer' => $this->findCustomer($sender),
                    'lead_id' => $lead?->id,
                    'is_pinned' => (bool) $preference?->is_pinned,
                    'is_muted' => (bool) $preference?->is_muted,
                    'is_archived' => (bool) $preference?->is_archived,
                    'mode' => $conversation?->mode ?? WhatsappConversation::MODE_BOT,
                    'handled_by' => $conversation?->handler?->name,
                    'bot_turns' => (int) ($conversation?->bot_turns ?? 0),
                ];
            })
            ->values()
            ->sortByDesc(fn ($conversation) => ($conversation['is_pinned'] ? '1' : '0').$conversation['last_at'])
            ->values();

        return response()->json($rows);
    }

    public function messages(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);

        $limit = min(max($request->integer('limit', 50), 1), 100);
        $query = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->when($request->filled('before'), fn ($query) => $query->where('id', '<', $request->integer('before')))
            ->when($request->filled('q'), fn ($query) => $query->where('message_body', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('id');

        $messages = $query->limit($limit + 1)->get();
        $hasMore = $messages->count() > $limit;
        $messages = $messages->take($limit)->sortBy('id')->values()
            ->map(fn ($m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'status' => $m->status,
                'message_body' => $m->message_body,
                'is_bot' => (bool) $m->is_bot,
                'created_at' => $m->created_at->format('d M H:i'),
                'created_iso' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'messages' => $messages,
            'has_more' => $hasMore,
            'next_before' => $hasMore ? $messages->first()['id'] : null,
            'customer' => $this->findCustomer($sender),
        ]);
    }

    public function contacts(Request $request, WhatsappAccount $account)
    {
        $this->authorizeAccount($account);

        $term = trim((string) $request->input('q', ''));
        $customers = Customer::query()
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->whereLike(['name', 'company', 'whatsapp', 'phone', 'email'], $term);
                });
            })
            ->where(function ($query) {
                $query->whereNotNull('whatsapp')->orWhereNotNull('phone');
            })
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (Customer $customer) => $this->contactPayload($customer));

        return response()->json($customers->values());
    }

    public function saveContact(Request $request, WhatsappAccount $account)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'whatsapp' => 'required|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        $customer = $this->findCustomer($validated['whatsapp']);

        if (! $customer) {
            $company = $validated['company'] ?? null;
            $companyName = $company ?: $validated['name'];
            $customer = Customer::create([
                'name' => $companyName,
                'company' => $companyName,
                'contact_person' => $validated['name'],
                'whatsapp' => $validated['whatsapp'],
                'notes' => $validated['notes'] ?? null,
            ]);
        } else {
            $update = [
                'contact_person' => $validated['name'],
                'whatsapp' => $validated['whatsapp'],
                'notes' => $validated['notes'] ?? $customer->notes,
            ];

            if (! empty($validated['company'])) {
                $update['name'] = $validated['company'];
                $update['company'] = $validated['company'];
            }

            $customer->update($update);
        }

        return response()->json($this->contactPayload($customer->fresh()));
    }

    public function markRead(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);

        $read = $request->boolean('read', true);
        $updated = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->where('direction', 'inbound')
            ->when($read, fn ($query) => $query->whereNull('read_at'))
            ->update(['read_at' => $read ? now() : null]);

        return response()->json(['updated' => $updated, 'read' => $read]);
    }

    public function preference(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'is_pinned' => 'sometimes|boolean',
            'is_muted' => 'sometimes|boolean',
            'is_archived' => 'sometimes|boolean',
        ]);

        $preference = WhatsappConversationPreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'whatsapp_account_id' => $account->id,
                'sender_number' => $sender,
            ],
            $validated,
        );

        return response()->json($preference->only(['is_pinned', 'is_muted', 'is_archived']));
    }

    public function store(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $validated = $request->validate([
            'message_body' => 'required|string|max:4000',
        ]);

        $sender = $this->normalizeNumber($sender);

        $message = WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $sender,
            'sender_name' => WhatsappMessage::where('whatsapp_account_id', $account->id)
                ->where('sender_number', $sender)->first()?->sender_name,
            'message_body' => $validated['message_body'],
            'direction' => 'outbound',
            'status' => 'queued',
        ]);

        if (! $this->gateway->configured($account)) {
            $status = 'queued';
        } elseif ($gatewayMessageId = $this->gateway->sendText($account, $sender, $validated['message_body'])) {
            $message->update([
                'gateway_message_id' => $gatewayMessageId,
                'status' => 'sent',
            ]);
            $status = 'sent';
        } else {
            $message->update(['status' => 'failed']);
            $status = 'failed';
        }

        // Marketing ikut campur manual → ambil alih dari bot (sticky).
        $this->bot->takeover($account, $sender, auth()->user());

        return response()->json([
            'id' => $message->id,
            'direction' => 'outbound',
            'status' => $status,
            'message_body' => $message->message_body,
            'created_at' => $message->created_at->format('d M H:i'),
        ]);
    }

    public function takeover(WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $conversation = $this->bot->takeover($account, $this->normalizeNumber($sender), auth()->user());

        return response()->json([
            'mode' => $conversation->mode,
            'handled_by' => auth()->user()->name,
            'bot_turns' => $conversation->bot_turns,
        ]);
    }

    public function release(WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $conversation = $this->bot->release($account, $this->normalizeNumber($sender));

        return response()->json([
            'mode' => $conversation->mode,
            'handled_by' => null,
            'bot_turns' => $conversation->bot_turns,
        ]);
    }

    public function convert(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'segment' => 'required|in:'.implode(',', LeadController::SEGMENTS),
            'kebutuhan' => 'nullable|string|max:2000',
        ]);

        $number = $this->normalizeNumber($sender);
        $customer = $this->findCustomer($number);

        if (! $customer) {
            $customer = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_name'],
                'whatsapp' => $sender,
                'contact_person' => $validated['customer_name'],
            ]);
        }

        $ptGroup = strtoupper(substr($account->account_code, 3));
        if (! in_array($ptGroup, Lead::PT_GROUPS)) {
            $ptGroup = Lead::PT_GROUPS[0];
        }

        $lead = Lead::create([
            'customer_id' => $customer->id,
            'pt_group' => $ptGroup,
            'whatsapp_account_id' => $account->id,
            'segment' => $validated['segment'],
            'source' => 'whatsapp',
            'status' => 'new',
            'kebutuhan' => $validated['kebutuhan'] ?? null,
            'incoming_date' => now()->toDateString(),
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'changes' => ['source' => 'whatsapp', 'sender' => $sender],
        ]);

        WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->where('direction', 'inbound')
            ->whereNull('lead_id')
            ->latest('id')
            ->first()
            ?->update(['lead_id' => $lead->id]);

        if (empty($lead->assigned_to)) {
            Notification::send(
                User::permission('manage-sales-leads')->get(),
                new NewLeadNotification($lead)
            );
        }

        return response()->json(['lead_id' => $lead->id, 'redirect' => route('leads.show', $lead)]);
    }

    public function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe'
            && $token
            && hash_equals(config('whatsapp.meta.verify_token'), $token)) {
            return response($challenge);
        }

        abort(403, 'Verifikasi webhook Meta gagal.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->json()->all();

        if (data_get($payload, 'entry.0.changes.0.value')) {
            return $this->handleMetaNotification($payload);
        }

        return $this->handleNotification($payload);
    }

    /**
     * Proses notifikasi Green API (dipakai webhook & polling receiveNotification).
     */
    public function handleNotification(array $payload): JsonResponse
    {
        $type = data_get($payload, 'typeWebhook');

        if (! $type) {
            return $this->storeInboundFromLegacy($payload);
        }

        $instance = data_get($payload, 'instanceData.idInstance');
        $account = $instance
            ? WhatsappAccount::where('gateway_instance', $instance)->first()
            : null;

        if (! $account) {
            return response()->json(['error' => 'unknown instance'], 422);
        }

        return match ($type) {
            'incomingMessageReceived' => $this->handleIncoming($account, $payload),
            'outgoingMessageStatus' => $this->handleOutgoingStatus($account, $payload),
            'instanceStatus' => $this->handleInstanceStatus($account, $payload),
            default => response()->json(['status' => 'ignored', 'type' => $type]),
        };
    }

    /**
     * Proses notifikasi webhook Meta WhatsApp Business Cloud API.
     * Akun dituju dicocokkan lewat value.metadata.phone_number_id
     * dengan kolom gateway_instance, sehingga mendukung banyak akun Meta.
     */
    public function handleMetaNotification(array $payload): JsonResponse
    {
        foreach (data_get($payload, 'entry.0.changes', []) as $change) {
            $value = data_get($change, 'value', []);
            $phoneNumberId = data_get($value, 'metadata.phone_number_id');

            if (! $phoneNumberId) {
                return response()->json(['error' => 'missing phone_number_id'], 422);
            }

            $account = WhatsappAccount::where('gateway_type', WhatsappAccount::GATEWAY_META)
                ->where('gateway_instance', $phoneNumberId)
                ->where('is_active', true)
                ->first();

            if (! $account) {
                return response()->json(['error' => 'unknown account'], 422);
            }

            foreach (data_get($value, 'statuses', []) as $status) {
                $id = data_get($status, 'id');
                WhatsappMessage::where('whatsapp_account_id', $account->id)
                    ->where(function ($q) use ($id) {
                        $q->where('wa_message_id', $id)
                            ->orWhere('gateway_message_id', $id);
                    })
                    ->update(['status' => data_get($status, 'status')]);
            }

            foreach (data_get($value, 'messages', []) as $msg) {
                $this->storeMetaInbound($account, $msg);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function storeMetaInbound(WhatsappAccount $account, array $msg): void
    {
        $messageId = data_get($msg, 'id');

        if ($messageId && WhatsappMessage::where('wa_message_id', $messageId)->exists()) {
            return;
        }

        $text = null;
        if (data_get($msg, 'type') === 'text') {
            $text = data_get($msg, 'text.body');
        }

        if ($text === null) {
            return;
        }

        $waId = data_get($msg, 'from');
        $senderName = data_get($msg, 'from') ?? 'Kontak WA';
        $timestamp = data_get($msg, 'timestamp');

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $waId,
            'sender_name' => $senderName,
            'message_body' => $text,
            'direction' => 'inbound',
            'status' => 'inbound',
            'wa_message_id' => $messageId,
            'created_at' => $timestamp ? now()->setTimestamp((int) $timestamp) : now(),
        ]);

        $this->notifyInbound($account, (string) $waId, $text);
    }

    public function updateCredentials(Request $request, WhatsappAccount $account)
    {
        if (! auth()->user()->hasRole('super-admin') && ! auth()->user()->hasPermissionTo('manage-sales-leads')) {
            abort(403);
        }

        $validated = $request->validate([
            'gateway_instance' => 'nullable|string|max:255',
            'gateway_token' => 'nullable|string|max:255',
            'bot_enabled' => 'nullable|boolean',
        ]);

        $account->update(array_merge($validated, [
            'bot_enabled' => $request->boolean('bot_enabled'),
            'gateway_status' => null,
        ]));

        if ($state = $this->gateway->getState($account)) {
            $account->update(['gateway_status' => $state]);
        }

        return redirect()->route('whatsapp-center.index')
            ->with('success', 'Kredensial gateway disimpan.');
    }

    private function handleIncoming(WhatsappAccount $account, array $payload): JsonResponse
    {
        $messageData = data_get($payload, 'messageData', []);
        $type = data_get($messageData, 'typeMessage');

        $text = match ($type) {
            'textMessage' => data_get($messageData, 'textMessageData.textMessage'),
            'extendedTextMessage' => data_get($messageData, 'extendedTextMessageData.text'),
            default => null,
        };

        if ($text === null) {
            return response()->json(['status' => 'ignored', 'type' => $type]);
        }

        $chatId = data_get($payload, 'senderData.chatId', '');
        $idMessage = data_get($payload, 'idMessage');
        $senderNumber = preg_replace('/@.*$/', '', $chatId);

        if ($idMessage && WhatsappMessage::where('wa_message_id', $idMessage)->exists()) {
            return response()->json(['status' => 'duplicate']);
        }

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $senderNumber,
            'sender_name' => data_get($payload, 'senderData.senderName') ?? 'Kontak WA',
            'message_body' => $text,
            'direction' => 'inbound',
            'status' => 'inbound',
            'wa_message_id' => $idMessage,
            'created_at' => data_get($payload, 'timestamp') ? now()->setTimestamp(data_get($payload, 'timestamp')) : now(),
        ]);

        $this->notifyInbound($account, $senderNumber, $text);

        return response()->json(['status' => 'ok']);
    }

    private function handleOutgoingStatus(WhatsappAccount $account, array $payload): JsonResponse
    {
        WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('gateway_message_id', data_get($payload, 'idMessage'))
            ->update(['status' => data_get($payload, 'status')]);

        return response()->json(['status' => 'ok']);
    }

    private function handleInstanceStatus(WhatsappAccount $account, array $payload): JsonResponse
    {
        $state = data_get($payload, 'stateInstance') ?? data_get($payload, 'body.stateInstance');

        if ($state) {
            $account->update(['gateway_status' => $state]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function storeInboundFromLegacy(array $payload): JsonResponse
    {
        $validated = Validator::make($payload, [
            'account_code' => 'required|string',
            'sender_number' => 'required|string',
            'sender_name' => 'nullable|string|max:255',
            'message_body' => 'required|string',
            'wa_message_id' => 'nullable|string',
        ])->validate();

        $account = WhatsappAccount::where('account_code', $validated['account_code'])->first();

        if (! $account) {
            return response()->json(['error' => 'unknown account'], 422);
        }

        if (! empty($validated['wa_message_id'])
            && WhatsappMessage::where('wa_message_id', $validated['wa_message_id'])->exists()) {
            return response()->json(['status' => 'duplicate']);
        }

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $validated['sender_number'],
            'sender_name' => $validated['sender_name'] ?? null,
            'message_body' => $validated['message_body'],
            'direction' => 'inbound',
            'wa_message_id' => $validated['wa_message_id'] ?? null,
        ]);

        $this->notifyInbound($account, (string) $validated['sender_number'], $validated['message_body']);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Notifikasi ke tim marketing saat percakapan baru masuk (sekali per 6 jam).
     */
    private function notifyInbound(WhatsappAccount $account, string $sender, string $preview): void
    {
        $recent = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->where('direction', 'inbound')
            ->where('created_at', '>=', now()->subHours(6))
            ->count();

        if ($recent > 1) {
            return;
        }

        Notification::send(
            User::permission('manage-marketing')->get(),
            new WhatsappInboundNotification(
                $account->id,
                $account->name ?: $account->account_code,
                $sender,
                mb_substr($preview, 0, 120),
            )
        );
    }

    public function simulate(Request $request, WhatsappAccount $account)
    {
        if (app()->isProduction()) {
            abort(404);
        }

        $this->authorizeAccount($account);

        $validated = $request->validate([
            'sender_number' => 'required|string',
            'sender_name' => 'nullable|string|max:255',
            'message_body' => 'required|string|max:4000',
        ]);

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $validated['sender_number'],
            'sender_name' => $validated['sender_name'] ?? 'Kontak Baru',
            'message_body' => $validated['message_body'],
            'direction' => 'inbound',
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function visibleAccounts()
    {
        $user = auth()->user();

        return WhatsappAccount::where('is_active', true)
            ->when(
                ! $user->hasRole('super-admin') && ! $user->hasPermissionTo('manage-sales-leads'),
                fn ($q) => $q->where('assigned_to', $user->id)
            )
            ->orderBy('account_code');
    }

    private function authorizeAccount(WhatsappAccount $account): void
    {
        if (! $this->visibleAccounts()->whereKey($account->id)->exists()) {
            abort(403);
        }
    }

    private function unreadCount(WhatsappAccount $account): int
    {
        return WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('direction', 'inbound')
            ->orderBy('id')
            ->get()
            ->groupBy('sender_number')
            ->sum(function ($messages) {
                $lastOutboundId = $messages->where('direction', 'outbound')->last()?->id ?? 0;

                return $messages->where('id', '>', $lastOutboundId)->whereNull('read_at')->count();
            });
    }

    private function findCustomer(string $number): ?Customer
    {
        $normalized = $this->normalizeNumber($number);

        if (! $normalized) {
            return null;
        }

        return Customer::whereNotNull('whatsapp')->orWhereNotNull('phone')->get()
            ->first(fn ($c) => $this->normalizeNumber($c->whatsapp ?? '') === $normalized
                || $this->normalizeNumber($c->phone ?? '') === $normalized);
    }

    private function normalizeNumber(string $number): string
    {
        return WhatsappGateway::normalizeNumber($number);
    }

    private function contactPayload(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'contact_person' => $customer->contact_person,
            'company' => $customer->company,
            'whatsapp' => $customer->whatsapp ?? $customer->phone,
            'notes' => $customer->notes,
            'url' => route('customers.show', $customer),
        ];
    }
}
