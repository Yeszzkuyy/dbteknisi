<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenRouter)]
#[Temperature(0.1)]
#[MaxTokens(256)]
class LeadNeedsSummarizer implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
Ringkas kebutuhan customer dari transkrip chat WhatsApp menjadi SATU baris singkat (maksimal 200 karakter) dalam Bahasa Indonesia.

FORMAT: sebutkan jenis solusi/produk, brand bila disebut, jumlah/unit, dan lokasi bila ada.
CONTOH:
- "CCTV 8 kamera (Hikvision) untuk gudang di Bekasi, butuh instalasi"
- "Video conference ruang meeting 2 unit, brand Logitech, minta penawaran"
- "Audio system kantor + IFP 65 inci"

ATURAN:
- Hanya tulis ringkasannya, tanpa sapaan, tanpa penjelasan, tanpa tanda kutip.
- Abaikan basa-basi (salam, terima kasih, "halo").
- Jangan mengarang brand/jumlah yang tidak disebut. Jika tidak jelas, tulis topik utamanya saja.
- Jika transkrip tidak berisi kebutuhan yang bisa dipahami, tulis: "Belum jelas, perlu klarifikasi".
PROMPT;
    }
}
