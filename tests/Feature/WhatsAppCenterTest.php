<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}