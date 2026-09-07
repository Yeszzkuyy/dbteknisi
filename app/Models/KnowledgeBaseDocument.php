<?php

namespace App\Models;

use App\Enums\KnowledgeBaseCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'category',
        'project_id',
        'user_id',
        'store_id',
        'provider_file_id',
        'provider_document_id',
        'mime',
        'size',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'category' => KnowledgeBaseCategory::class,
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
