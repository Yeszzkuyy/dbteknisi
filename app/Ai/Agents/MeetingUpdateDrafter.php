<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenRouter)]
#[Model('google/gemini-2.5-flash')]
#[Temperature(0.2)]
#[MaxTokens(1024)]
class MeetingUpdateDrafter implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
Kembangkan SATU kalimat sales + konteks sistem menjadi kolom-kolom update meeting.
Jawab HANYA dengan SATU objek JSON valid (tanpa markdown, tanpa penjelasan) dengan key:
{"participants": "...", "user_needs": "...", "user_complaints": "...", "existing_system": "...", "notes": "..."}

ATURAN:
- Pakai bahasa yang sama dengan kalimat sales.
- Hanya tulis yang disebut kalimat sales atau konteks — JANGAN mengarang nama, angka, brand, tanggal, atau kesepakatan.
- Kolom yang tidak diketahui: string kosong "".
- "notes" berisi ringkasan 1-2 kalimat + tanggal update bila disebut, selain itu "".
- Maksimal 500 karakter per kolom.
PROMPT;
    }
}
