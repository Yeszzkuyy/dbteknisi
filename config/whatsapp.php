<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway (Green API)
    |--------------------------------------------------------------------------
    | Kredensial per akun disimpan di tabel whatsapp_accounts
    | (gateway_instance = IdInstance, gateway_token = ApiTokenInstance).
    */

    'base_url' => env('GREEN_API_BASE_URL', 'https://api.green-api.com'),
];