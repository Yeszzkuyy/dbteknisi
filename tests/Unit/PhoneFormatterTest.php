<?php

namespace Tests\Unit;

use App\Support\PhoneFormatter;
use PHPUnit\Framework\TestCase;

class PhoneFormatterTest extends TestCase
{
    public function test_formats_indonesian_numbers(): void
    {
        $this->assertSame('0819-8769-8658', PhoneFormatter::format('081987698658'));
        $this->assertSame('021-1231-3435', PhoneFormatter::format('02112313435'));
        $this->assertSame('0819-8769-8658', PhoneFormatter::format('0819 8769 8658'));
        $this->assertSame('+62 812-3456', PhoneFormatter::format('+62 812-3456'));
        $this->assertNull(PhoneFormatter::format(''));
    }
}