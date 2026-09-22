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
        'pt_group' => 'PT',
        'file' => 'Lampiran',
        'assigned_to' => 'Sales',
        'customer_id' => 'Customer',
        'segment' => 'Segment',
        'status' => 'Status',
        'source' => 'Sumber Lead',
        'kebutuhan' => 'Kebutuhan',
        'notes' => 'Catatan',
        'incoming_date' => 'Tanggal Masuk',
        'sender' => 'Pengirim WA',
    ];

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created' => __('Menambahkan lead'),
            'updated' => __('Mengubah lead'),
            'deleted' => __('Menghapus lead'),
            'attachment_deleted' => __('Menghapus lampiran lead'),
            'converted' => __('Mengonversi lead menjadi Project'),
            'status_changed' => __('Memindahkan posisi lead di pipeline'),
            'assigned' => __('Meng-assign lead ke sales'),
            default => $this->action,
        };
    }
}
