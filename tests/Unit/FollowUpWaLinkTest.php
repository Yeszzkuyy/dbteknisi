<?php

namespace Tests\Unit;

use App\Models\Customer;
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
}
