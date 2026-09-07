<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi No WhatsApp: hitung jumlah digit, bukan panjang string.
 *
 * - Awalan kode negara +62 tidak dihitung.
 * - Tanda +, -, spasi, dan karakter non-digit lainnya diabaikan.
 * - Digit yang tersisa harus berjumlah 10–13.
 */
class WhatsappNumber implements ValidationRule
{
    public function validate($attribute, $value, Closure $fail): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        $cleaned = preg_replace('/^\+62/', '', trim((string) $value));
        $digits = preg_replace('/\D/', '', $cleaned);

        if (strlen($digits) < 10 || strlen($digits) > 13) {
            $fail('No WA harus berisi 10–13 digit. Awalan +62 tidak dihitung, tanda + / - diabaikan.');
        }
    }
}