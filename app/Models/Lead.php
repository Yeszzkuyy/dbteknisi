<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const PT_GROUPS = ['NTI', 'MGK', 'TPS', 'WANI'];

    public const PT_COLORS = [
        'NTI' => 'bg-sky-500 text-white',
        'MGK' => 'bg-blue-900 text-white',
        'TPS' => 'bg-red-900 text-white',
        'WANI' => 'bg-orange-500 text-white',
    ];

    protected $fillable = [
        'customer_id',
        'pt_group',
        'segment',
        'status',
        'source',
        'kebutuhan',
        'notes',
        'incoming_date',
        'assigned_to',
    ];

    protected $casts = [
        'incoming_date' => 'date',
    ];

    public function customer()
    {
        // Lead tetap harus menampilkan customernya walau customer sudah soft-deleted
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function projects()
    {
        return $this->customer->projects();
    }
}