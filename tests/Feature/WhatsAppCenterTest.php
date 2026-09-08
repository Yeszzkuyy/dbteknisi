<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppCenterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function marketingUser(?int $accountId = null)
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('marketing');

        if ($accountId) {
            WhatsappAccount::whereKey($accountId)->update(['assigned_to' => $user->id]);
        }

        return $user;
    }

    private function makeAccount(string $code = 'wa_nti'): WhatsappAccount
    {
        return WhatsappAccount::create([
            'name' => "WA {$code}",
            'phone_number' => '6281111111101',
            'account_code' => $code,
            'assigned_to' => null,
            'is_active' => true,
        ]);
    }

    public function test_webhook_stores_inbound_and_dedupes(): void
    {
        $account = $this->makeAccount();

        $payload = [
            'account_code' => 'wa_nti',
            'sender_number' => '6281234567890',
            'sender_name' => 'Rina',
            'message_body' => 'Halo, minta penawaran dong',
            'wa_message_id' => 'wa_msg_1',
        ];

        $this->postJson('/api/whatsapp/webhook', $payload)->assertOk();
        $this->postJson('/api/whatsapp/webhook', $payload)->assertOk();

        $this->assertSame(1, WhatsappMessage::count());
        $this->assertDatabaseHas('whatsapp_messages', [
            'whatsapp_account_id' => $account->id,
            'direction' => 'inbound',
        ]);
    }

    public function test_marketing_user_can_reply_and_convert_to_lead(): void
    {
        $account = $this->makeAccount();
        $user = $this->marketingUser($account->id);

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'sender_name' => 'Rina Putri',
            'message_body' => 'Butuh internet kantor',
            'direction' => 'inbound',
        ]);

        $this->actingAs($user)
            ->post(route('whatsapp-center.reply', [$account, '6281234567890']), ['message_body' => 'Baik, saya bantu'])
            ->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'outbound', 'message_body' => 'Baik, saya bantu']);

        $this->actingAs($user)
            ->post(route('whatsapp-center.convert', [$account, '6281234567890']), [
                'customer_name' => 'Rina Putri',
                'segment' => 'end_user',
                'kebutuhan' => 'Internet kantor',
            ])
            ->assertOk()
            ->assertJsonPath('lead_id', Lead::first()->id);

        $this->assertDatabaseHas('customers', ['whatsapp' => '6281234567890']);
        $this->assertDatabaseHas('leads', [
            'pt_group' => 'NTI',
            'source' => 'whatsapp',
            'whatsapp_account_id' => $account->id,
        ]);
        $this->assertNotNull(WhatsappMessage::first()->lead_id);
    }

    public function test_convert_reuses_existing_customer(): void
    {
        $account = $this->makeAccount('wa_wani');
        $user = $this->marketingUser($account->id);

        Customer::create(['name' => 'PT Lama', 'whatsapp' => '081234567890']);
        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'sender_name' => 'Pak Lama',
            'message_body' => 'Mau perpanjang',
            'direction' => 'inbound',
        ]);

        $this->actingAs($user)
            ->post(route('whatsapp-center.convert', [$account, '6281234567890']), [
                'customer_name' => 'Pak Lama',
                'segment' => 'vendor',
            ])
            ->assertOk();

        $this->assertSame(1, Customer::count());
        $this->assertDatabaseHas('leads', ['pt_group' => 'WANI']);
    }

    public function test_user_only_sees_own_accounts(): void
    {
        $mine = $this->makeAccount('wa_nti');
        $other = $this->makeAccount('wa_mgk');
        $user = $this->marketingUser($mine->id);

        $response = $this->actingAs($user)->get(route('whatsapp-center.index'))->assertOk();

        $accounts = collect($response->viewData('accounts'));
        $this->assertTrue($accounts->contains('id', $mine->id));
        $this->assertFalse($accounts->contains('id', $other->id));
    }

    public function test_user_cannot_access_account_belonging_to_others(): void
    {
        $mine = $this->makeAccount('wa_nti');
        $other = $this->makeAccount('wa_mgk');
        $user = $this->marketingUser($mine->id);

        $this->actingAs($user)->get(route('whatsapp-center.conversations', $other))->assertForbidden();
    }

    public function test_super_admin_sees_all_accounts(): void
    {
        $a = $this->makeAccount('wa_nti');
        $b = $this->makeAccount('wa_mgk');
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->get(route('whatsapp-center.index'))->assertOk();

        $accounts = collect($response->viewData('accounts'));
        $this->assertTrue($accounts->contains('id', $a->id));
        $this->assertTrue($accounts->contains('id', $b->id));
    }

    public function test_reply_sends_via_green_api_when_configured(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101', 'gateway_token' => 'tok-123']);
        $user = $this->marketingUser($account->id);

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'sender_name' => 'Rina',
            'message_body' => 'Halo',
            'direction' => 'inbound',
        ]);

        Http::fake([
            'api.green-api.com/*' => Http::response(['idMessage' => 'g_msg_1']),
        ]);

        $this->actingAs($user)
            ->post(route('whatsapp-center.reply', [$account, '6281234567890']), ['message_body' => 'Baik'])
            ->assertOk()
            ->assertJsonPath('status', 'sent');

        $this->assertDatabaseHas('whatsapp_messages', [
            'direction' => 'outbound',
            'status' => 'sent',
            'gateway_message_id' => 'g_msg_1',
        ]);
    }

    public function test_reply_stored_only_when_gateway_unconfigured(): void
    {
        $account = $this->makeAccount();
        $user = $this->marketingUser($account->id);

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'sender_name' => 'Rina',
            'message_body' => 'Halo',
            'direction' => 'inbound',
        ]);

        $this->actingAs($user)
            ->post(route('whatsapp-center.reply', [$account, '6281234567890']), ['message_body' => 'Baik'])
            ->assertOk()
            ->assertJsonPath('status', 'queued');

        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'outbound', 'status' => 'queued']);
    }

    public function test_green_api_webhook_stores_inbound_text(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101']);

        $payload = [
            'typeWebhook' => 'incomingMessageReceived',
            'instanceData' => ['idInstance' => 1101],
            'body' => [
                'idMessage' => 'A1B2C3',
                'timestamp' => now()->timestamp,
                'senderData' => ['chatId' => '6281234567890@c.us', 'senderName' => 'Rina Putri'],
                'messageData' => ['typeMessage' => 'textMessage', 'textMessageData' => ['textMessage' => 'Minta penawaran']],
            ],
        ];

        $this->postJson('/api/whatsapp/webhook', $payload)->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'sender_name' => 'Rina Putri',
            'message_body' => 'Minta penawaran',
            'wa_message_id' => 'A1B2C3',
        ]);

        // Duplicate webhook ditolak via idMessage
        $this->postJson('/api/whatsapp/webhook', $payload)->assertOk();
        $this->assertSame(1, WhatsappMessage::count());
    }

    public function test_green_api_webhook_updates_outgoing_status(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101']);

        WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'message_body' => 'Baik',
            'direction' => 'outbound',
            'status' => 'sent',
            'gateway_message_id' => 'g_msg_1',
        ]);

        $this->postJson('/api/whatsapp/webhook', [
            'typeWebhook' => 'outgoingMessageStatus',
            'instanceData' => ['idInstance' => 1101],
            'body' => ['idMessage' => 'g_msg_1', 'status' => 'delivered'],
        ])->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', ['gateway_message_id' => 'g_msg_1', 'status' => 'delivered']);
    }

    public function test_green_api_webhook_updates_instance_status(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101']);

        $this->postJson('/api/whatsapp/webhook', [
            'typeWebhook' => 'instanceStatus',
            'instanceData' => ['idInstance' => 1101],
            'body' => ['stateInstance' => 'authorized'],
        ])->assertOk();

        $this->assertDatabaseHas('whatsapp_accounts', ['id' => $account->id, 'gateway_status' => 'authorized']);
    }

    public function test_green_api_webhook_rejects_unknown_instance(): void
    {
        $this->makeAccount();
        $this->postJson('/api/whatsapp/webhook', [
            'typeWebhook' => 'incomingMessageReceived',
            'instanceData' => ['idInstance' => 9999],
            'body' => [],
        ])->assertStatus(422);
    }

    public function test_super_admin_can_update_gateway_credentials(): void
    {
        $account = $this->makeAccount();
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');

        Http::fake([
            'api.green-api.com/waInstance1102/getStateInstance/*' => Http::response(['stateInstance' => 'authorized']),
        ]);

        $this->actingAs($admin)
            ->put(route('whatsapp-center.credentials', $account), [
                'gateway_instance' => '1102',
                'gateway_token' => 'tok-abc',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('whatsapp_accounts', [
            'id' => $account->id,
            'gateway_instance' => '1102',
            'gateway_status' => 'authorized',
        ]);
    }

    public function test_marketing_user_cannot_update_gateway_credentials(): void
    {
        $account = $this->makeAccount();
        $user = $this->marketingUser($account->id);

        $this->actingAs($user)
            ->put(route('whatsapp-center.credentials', $account), ['gateway_instance' => '999'])
            ->assertForbidden();
    }

    public function test_green_api_webhook_stores_extended_text_message(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101']);

        $this->postJson('/api/whatsapp/webhook', [
            'typeWebhook' => 'incomingMessageReceived',
            'instanceData' => ['idInstance' => 1101],
            'body' => [
                'idMessage' => 'EXT1',
                'timestamp' => now()->timestamp,
                'senderData' => ['chatId' => '6281234567891@c.us', 'senderName' => 'Budi'],
                'messageData' => ['typeMessage' => 'extendedTextMessage', 'extendedTextMessageData' => ['text' => 'Pesan dari extendedTextMessage']],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'whatsapp_account_id' => $account->id,
            'message_body' => 'Pesan dari extendedTextMessage',
        ]);
    }

    public function test_receive_command_polls_inbound_and_acknowledges(): void
    {
        $account = $this->makeAccount();
        $account->update(['gateway_instance' => '1101', 'gateway_token' => 'tok-123']);

        Http::fake([
            'api.green-api.com/waInstance1101/getStateInstance/*' => Http::response(['stateInstance' => 'authorized']),
            'api.green-api.com/waInstance1101/receiveNotification/*' => Http::response([
                'receiptId' => 5,
                'body' => [
                    'typeWebhook' => 'incomingMessageReceived',
                    'instanceData' => ['idInstance' => 1101],
                    'body' => [
                        'idMessage' => 'POLL1',
                        'timestamp' => now()->timestamp,
                        'senderData' => ['chatId' => '6281234567890@c.us', 'senderName' => 'Rina Poll'],
                        'messageData' => ['typeMessage' => 'textMessage', 'textMessageData' => ['textMessage' => 'Halo dari polling']],
                    ],
                ],
            ]),
            'api.green-api.com/waInstance1101/deleteNotification/*' => Http::response(['result' => true]),
        ]);

        $this->artisan('whatsapp:receive')->assertExitCode(0);

        // Status koneksi ikut diperbarui dari getStateInstance
        $this->assertDatabaseHas('whatsapp_accounts', ['id' => $account->id, 'gateway_status' => 'authorized']);

        // Notifikasi yang sama hanya disimpan sekali (dedupe idMessage)
        $this->assertSame(1, WhatsappMessage::count());
        $this->assertDatabaseHas('whatsapp_messages', [
            'whatsapp_account_id' => $account->id,
            'sender_number' => '6281234567890',
            'message_body' => 'Halo dari polling',
            'wa_message_id' => 'POLL1',
        ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/deleteNotification/'));
    }

    public function test_receive_command_skips_account_without_credentials(): void
    {
        $this->makeAccount();
        Http::fake();

        $this->artisan('whatsapp:receive')->assertExitCode(0);
        Http::assertNothingSent();
    }
}