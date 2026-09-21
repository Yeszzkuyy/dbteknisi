<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    public const MODE_BOT = 'bot';

    public const MODE_HUMAN = 'human';

    protected $fillable = [
        'whatsapp_account_id',
        'sender_number',
        'mode',
        'handled_by',
        'bot_turns',
        'needs_summary',
        'taken_over_at',
        'reminded_at',
    ];

    protected $casts = [
        'bot_turns' => 'integer',
        'taken_over_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isHuman(): bool
    {
        return $this->mode === self::MODE_HUMAN;
    }
}
