<?php

namespace Tests\Unit;

use App\Rules\WhatsappNumber;
use PHPUnit\Framework\TestCase;

class WhatsappNumberTest extends TestCase
{
    private function validate($value): bool
    {
        $failed = false;
        (new WhatsappNumber)->validate('whatsapp', $value, function () use (&$failed) {
            $failed = true;
        });

        return ! $failed;
    }

    public function test_accepts_valid_numbers(): void
    {
        $this->assertTrue($this->validate('081234567890'));
        $this->assertTrue($this->validate('+6281234567890'));
        $this->assertTrue($this->validate('0812-3456-7890'));
        $this->assertTrue($this->validate('+62 812-3456-7890'));
        $this->assertTrue($this->validate(''));
        $this->assertTrue($this->validate(null));
    }

    public function test_rejects_wrong_digit_count(): void
    {
        $this->assertFalse($this->validate('081234'));
        $this->assertFalse($this->validate('+6281234567890123'));
    }
}