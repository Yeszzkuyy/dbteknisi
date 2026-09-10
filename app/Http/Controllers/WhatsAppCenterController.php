<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use App\Notifications\NewLeadNotification;
use App\Services\WhatsappGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class WhatsAppCenterController extends Controller
{
    public function __construct(
        private readonly WhatsappGateway $gateway,
    ) {
    }

    public function index()
    {
        $accounts = $this->visibleAccounts()->get();
        $accountTabs = $accounts->map(fn ($a) => [
            'id' => $a->id,
            'account_code' => $a->account_code,
            'label' => 'WA ' . strtoupper(substr($a->account_code, 3)),
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
        if (!$this->gateway->isMeta($account)) {
            return response()->json([
                'gateway_status' => $account->gateway_status,
            ]);
        }

        $state = $this->gateway->getState($account);
        $status = $state ?? $account->gateway_status;

        if ($status !== $account->gateway_status) {
            $account->update(['gateway_status' => $status]);
        }

        return response()->json(['gateway_status' => $status]);
    }

    public function conversations(WhatsappAccount $account)
    {
        $this->authorizeAccount($account);

        $rows = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->orderBy('id')
            ->get()
            ->groupBy('sender_number')
            ->map(function ($messages, $sender) {
                $last = $messages->last();
                $lastOutboundId = $messages->where('direction', 'outbound')->last()?->id ?? 0;
                $lead = $messages->whereNotNull('lead_id')->first()?->lead;

                return [
                    'sender_number' => $sender,
                    'sender_name' => $last->sender_name ?? 'Kontak WA',
                    'last_message' => $last->message_body,
                    'last_direction' => $last->direction,
                    'last_at' => $last->created_at->toIso8601String(),
                    'unread' => $messages->where('direction', 'inbound')->where('id', '>', $lastOutboundId)->count(),
                    'customer' => $this->findCustomer($sender),
                    'lead_id' => $lead?->id,
                ];
            })
            ->values()
            ->sortByDesc('last_at')
            ->values();

        return response()->json($rows);
    }

    public function messages(WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);

        $messages = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'status' => $m->status,
                'message_body' => $m->message_body,
                'created_at' => $m->created_at->format('d M H:i'),
            ]);

        return response()->json([
            'messages' => $messages,
            'customer' => $this->findCustomer($sender),
        ]);
    }

    public function store(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $validated = $request->validate([
            'message_body' => 'required|string|max:4000',
        ]);

        $message = WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $sender,
            'sender_name' => WhatsappMessage::where('whatsapp_account_id', $account->id)
                ->where('sender_number', $sender)->first()?->sender_name,
            'message_body' => $validated['message_body'],
            'direction' => 'outbound',
            'status' => 'queued',
        ]);

        if (!$this->gateway->configured($account)) {
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

        return response()->json([
            'id' => $message->id,
            'direction' => 'outbound',
            'status' => $status,
            'message_body' => $message->message_body,
            'created_at' => $message->created_at->format('d M H:i'),
        ]);
    }

    public function convert(Request $request, WhatsappAccount $account, string $sender)
    {
        $this->authorizeAccount($account);
        $this->authorize('manage-marketing');

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'segment' => 'required|in:' . implode(',', LeadController::SEGMENTS),
            'kebutuhan' => 'nullable|string|max:2000',
        ]);

        $number = $this->normalizeNumber($sender);
        $customer = $this->findCustomer($number);

        if (!$customer) {
            $customer = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_name'],
                'whatsapp' => $sender,
                'contact_person' => $validated['customer_name'],
            ]);
        }

        $ptGroup = strtoupper(substr($account->account_code, 3));
        if (!in_array($ptGroup, Lead::PT_GROUPS)) {
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
    public function handleNotification(array $payload): \Illuminate\Http\JsonResponse
    {
        $type = data_get($payload, 'typeWebhook');

        if (!$type) {
            return $this->storeInboundFromLegacy($payload);
        }

        $instance = data_get($payload, 'instanceData.idInstance');
        $account = $instance
            ? WhatsappAccount::where('gateway_instance', $instance)->first()
            : null;

        if (!$account) {
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
     */
    public function handleMetaNotification(array $payload): \Illuminate\Http\JsonResponse
    {
        $account = WhatsappAccount::where('account_code', 'wa_wani')->first();

        if (!$account) {
            return response()->json(['error' => 'unknown account'], 422);
        }

        foreach (data_get($payload, 'entry.0.changes', []) as $change) {
            $value = data_get($change, 'value', []);

            foreach (data_get($value, 'statuses', []) as $status) {
                WhatsappMessage::where('whatsapp_account_id', $account->id)
                    ->where('wa_message_id', data_get($status, 'id'))
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
    }

    public function updateCredentials(Request $request, WhatsappAccount $account)
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage-sales-leads')) {
            abort(403);
        }

        $validated = $request->validate([
            'gateway_instance' => 'nullable|string|max:255',
            'gateway_token' => 'nullable|string|max:255',
        ]);

        $account->update(array_merge($validated, ['gateway_status' => null]));

        if ($state = $this->gateway->getState($account)) {
            $account->update(['gateway_status' => $state]);
        }

        return redirect()->route('whatsapp-center.index')
            ->with('success', 'Kredensial gateway disimpan.');
    }

    private function handleIncoming(WhatsappAccount $account, array $payload): \Illuminate\Http\JsonResponse
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

        return response()->json(['status' => 'ok']);
    }

    private function handleOutgoingStatus(WhatsappAccount $account, array $payload): \Illuminate\Http\JsonResponse
    {
        WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('gateway_message_id', data_get($payload, 'idMessage'))
            ->update(['status' => data_get($payload, 'status')]);

        return response()->json(['status' => 'ok']);
    }

    private function handleInstanceStatus(WhatsappAccount $account, array $payload): \Illuminate\Http\JsonResponse
    {
        $state = data_get($payload, 'stateInstance') ?? data_get($payload, 'body.stateInstance');

        if ($state) {
            $account->update(['gateway_status' => $state]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function storeInboundFromLegacy(array $payload): \Illuminate\Http\JsonResponse
    {
        $validated = \Illuminate\Support\Facades\Validator::make($payload, [
            'account_code' => 'required|string',
            'sender_number' => 'required|string',
            'sender_name' => 'nullable|string|max:255',
            'message_body' => 'required|string',
            'wa_message_id' => 'nullable|string',
        ])->validate();

        $account = WhatsappAccount::where('account_code', $validated['account_code'])->first();

        if (!$account) {
            return response()->json(['error' => 'unknown account'], 422);
        }

        if (!empty($validated['wa_message_id'])
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

        return response()->json(['status' => 'ok']);
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
                !$user->hasRole('super-admin') && !$user->hasPermissionTo('manage-sales-leads'),
                fn ($q) => $q->where('assigned_to', $user->id)
            )
            ->orderBy('account_code');
    }

    private function authorizeAccount(WhatsappAccount $account): void
    {
        if (!$this->visibleAccounts()->whereKey($account->id)->exists()) {
            abort(403);
        }
    }

    private function unreadCount(WhatsappAccount $account): int
    {
        return WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->orderBy('id')
            ->get()
            ->groupBy('sender_number')
            ->sum(function ($messages) {
                $lastOutboundId = $messages->where('direction', 'outbound')->last()?->id ?? 0;
                return $messages->where('direction', 'inbound')->where('id', '>', $lastOutboundId)->count();
            });
    }

    private function findCustomer(string $number): ?Customer
    {
        $normalized = $this->normalizeNumber($number);

        if (!$normalized) {
            return null;
        }

        return Customer::whereNotNull('whatsapp')->orWhereNotNull('phone')->get()
            ->first(fn ($c) => $this->normalizeNumber($c->whatsapp ?? '') === $normalized
                || $this->normalizeNumber($c->phone ?? '') === $normalized);
    }

    private function normalizeNumber(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}