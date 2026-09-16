<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConversationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'sender_number',
        'is_pinned',
        'is_muted',
        'is_archived',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_muted' => 'boolean',
        'is_archived' => 'boolean',
    ];
}
