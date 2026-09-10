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

    /*
    | Meta WhatsApp Business Cloud API.
    | verify_token dipakai untuk verifikasi webhook di dashboard Meta.
    */
    'meta' => [
        'api_version' => 'v19.0',
        'graph_base_url' => 'https://graph.facebook.com',
        'verify_token' => env('META_WA_VERIFY_TOKEN', ''),
    ],
];