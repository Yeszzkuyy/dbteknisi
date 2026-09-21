<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'whatsapp_account_id',
        'sender_number',
        'sender_name',
        'message_body',
        'direction',
        'status',
        'is_bot',
        'wa_message_id',
        'gateway_message_id',
        'lead_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_bot' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
