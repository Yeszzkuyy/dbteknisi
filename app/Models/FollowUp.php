<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'meeting_id',
        'description',
        'follow_up_date',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followUpWaLink(): ?string
    {
        if (!$this->customer) {
            return null;
        }

        $greeting = $this->customer->contact_person ?: $this->customer->name;
        $description = $this->description ? ' terkait "' . \Illuminate\Support\Str::limit($this->description, 80) . '"' : '';
        $date = $this->follow_up_date ? ' (jadwal ' . $this->follow_up_date->format('d M') . ')' : '';

        return $this->customer->waLink("Halo {$greeting}, izin follow up{$description}{$date}.");
    }
}
