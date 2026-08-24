<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public const FIELD_LABELS = [
        'customer_id' => 'Customer',
        'segment' => 'Segment',
        'status' => 'Status',
        'source' => 'Sumber Lead',
        'kebutuhan' => 'Kebutuhan User',
        'notes' => 'Catatan',
        'incoming_date' => 'Tanggal Masuk',
    ];

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Menambahkan lead',
            'updated' => 'Mengubah lead',
            'deleted' => 'Menghapus lead',
            'converted' => 'Mengonversi lead menjadi Project',
            default => $this->action,
        };
    }
}
