<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Database\Seeder;

class WhatsappAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'WA Sales NTI', 'phone_number' => '6281111111101', 'account_code' => 'wa_nti', 'assigned_to' => 'Syifa'],
            ['name' => 'WA Sales WANI', 'phone_number' => '6281111111102', 'account_code' => 'wa_wani', 'assigned_to' => 'Syifa'],
            ['name' => 'WA Sales MGK', 'phone_number' => '6281111111103', 'account_code' => 'wa_mgk', 'assigned_to' => 'Anggie'],
            ['name' => 'WA Sales TPS', 'phone_number' => '6281111111104', 'account_code' => 'wa_tps', 'assigned_to' => 'Anggie'],
        ];

        $created = [];
        foreach ($accounts as $a) {
            $user = User::where('name', $a['assigned_to'])->first();
            $account = WhatsappAccount::firstOrCreate(
                ['account_code' => $a['account_code']],
                [
                    'name' => $a['name'],
                    'phone_number' => $a['phone_number'],
                    'assigned_to' => $user?->id,
                    'is_active' => true,
                ]
            );
            $created[$account->account_code] = $account;
        }

        if (WhatsappMessage::where('sender_number', '6281234500001')->exists()) {
            return;
        }

        $now = now();
        $existingCustomer = Customer::whereNotNull('whatsapp')->orWhereNotNull('phone')->first();
        $existingNumber = preg_replace('/\D/', '', $existingCustomer?->whatsapp ?? $existingCustomer?->phone ?? '628123456789');

        $chats = [
            'wa_nti' => [
                ['sender_number' => $existingNumber, 'sender_name' => $existingCustomer?->name ?? 'Customer Existing', 'message_body' => 'Assalamualaikum, saya tanya paket internet kantor dong', 'sent_at' => $now->copy()->subMinutes(40)],
                ['sender_number' => $existingNumber, 'sender_name' => $existingCustomer?->name ?? 'Customer Existing', 'message_body' => 'Perusahaan kami butuh internet backup + SD-WAN', 'sent_at' => $now->copy()->subMinutes(38)],
                ['sender_number' => '6281234500001', 'sender_name' => 'Rina Putri', 'message_body' => 'Halo, saya dapat info dari teman. Bisa minta penawaran fiber optik untuk ruko?', 'sent_at' => $now->copy()->subMinutes(10)],
            ],
            'wa_wani' => [
                ['sender_number' => '6281234500002', 'sender_name' => 'Budi Santoso', 'message_body' => 'Mau pasang internet untuk 3 unit toko, bisa dibantu?', 'sent_at' => $now->copy()->subMinutes(25)],
            ],
            'wa_mgk' => [
                ['sender_number' => '6281234500003', 'sender_name' => 'Sari Dewi', 'message_body' => 'Kami butuh quote untuk instalasi CCTV kantor 2 lantai', 'sent_at' => $now->copy()->subMinutes(90)],
            ],
            'wa_tps' => [
                ['sender_number' => '6281234500004', 'sender_name' => 'Andi Wijaya', 'message_body' => 'Apakah bisa survey lokasi minggu ini?', 'sent_at' => $now->copy()->subMinutes(15)],
            ],
        ];

        foreach ($chats as $code => $messages) {
            foreach ($messages as $m) {
                WhatsappMessage::create([
                    'whatsapp_account_id' => $created[$code]->id,
                    'sender_number' => $m['sender_number'],
                    'sender_name' => $m['sender_name'],
                    'message_body' => $m['message_body'],
                    'direction' => 'inbound',
                    'created_at' => $m['sent_at'],
                    'updated_at' => $m['sent_at'],
                ]);
            }
        }
    }
}