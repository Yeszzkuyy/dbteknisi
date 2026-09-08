<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappAccount extends Model
{
    use SoftDeletes;

    public const GROUPS = ['NTI', 'MGK', 'TPS', 'WANI'];

    protected $fillable = [
        'name',
        'phone_number',
        'account_code',
        'assigned_to',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(WhatsappMessage::class);
    }
}