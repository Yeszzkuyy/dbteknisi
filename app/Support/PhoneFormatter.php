<?php

namespace App\Support;

class PhoneFormatter
{
    public static function format(?string $number): ?string
    {
        $number = trim((string) $number);

        if ($number === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '021') && strlen($digits) > 3) {
            return '021-'.implode('-', str_split(substr($digits, 3), 4));
        }

        if (str_starts_with($digits, '08') && strlen($digits) > 4) {
            return implode('-', str_split($digits, 4));
        }

        return $number;
    }
}