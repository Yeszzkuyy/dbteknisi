<?php

namespace App\Services;

class ConversationTitle
{
    /**
     * Rapikan judul percakapan untuk ditampilkan: kupas kutip,
     * buang sisa potongan "...", potong di batas kata (max 60 huruf).
     */
    public static function smart(?string $title, int $max = 60): string
    {
        $title = preg_replace('/\s+/u', ' ', trim((string) $title)) ?? '';

        // Kupas tanda kutip/pasangan pembungkus + pungtuasi akhir + sisa
        // potongan ("..."/"…") berulang sampai stabil.
        do {
            $before = $title;
            $title = trim(preg_replace('/^[\'"«»„“”‘’「」『』‹›\(\[]+|[\'"«»„“”‘’「」『』‹›\)\]]+$/u', '', $title) ?? '');
            $title = rtrim($title, " \t\n\r\0\x0B.,!?:;");
            $title = (string) preg_replace('/(\.{3,}|…)+$/u', '', $title);
        } while ($title !== $before);
        $title = rtrim($title, " \t\n\r\0\x0B.,!?:;");

        if ($title === '') {
            return __('Percakapan baru');
        }

        if (mb_strlen($title) <= $max) {
            return $title;
        }

        $cut = mb_substr($title, 0, $max);
        $space = mb_strrpos($cut, ' ');

        return $space !== false && $space > (int) ($max * 0.4)
            ? mb_substr($cut, 0, $space)
            : $cut;
    }
}
