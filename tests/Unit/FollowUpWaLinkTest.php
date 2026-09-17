<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\FollowUp;
use Tests\TestCase;

class FollowUpWaLinkTest extends TestCase
{
    public function test_normalizes_number_and_encodes_message(): void
    {
        $customer = new Customer(['name' => 'PT Uji', 'whatsapp' => '0812-3456-7890']);

        $this->assertSame(
            'https://wa.me/6281234567890?text=' . rawurlencode('Halo Budi'),
            $customer->waLink('Halo Budi')
        );
    }

    public function test_prefixes_local_eight_number_and_falls_back_to_phone(): void
    {
        $customer = new Customer(['name' => 'PT Uji', 'phone' => '81234567890']);

        $this->assertSame('https://wa.me/6281234567890', $customer->waLink());
    }

    public function test_returns_null_without_number(): void
    {
        $this->assertNull((new Customer(['name' => 'PT Uji']))->waLink('Halo'));
    }

    public function test_follow_up_link_uses_customer_and_context(): void
    {
        $customer = new Customer(['name' => 'PT Uji', 'contact_person' => 'Budi', 'whatsapp' => '6281234567890']);
        $followUp = new FollowUp(['description' => 'Kirim penawaran', 'follow_up_date' => '2026-09-20']);
        $followUp->setRelation('customer', $customer);

        $link = $followUp->followUpWaLink();

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $link);
        $this->assertStringContainsString(rawurlencode('Halo Budi'), $link);
        $this->assertStringContainsString(rawurlencode('Kirim penawaran'), $link);
    }
}
