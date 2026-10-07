<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const STATUS_REVIEW = 'review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'brand', 'name', 'model', 'sku', 'category', 'description', 'status',
    ];

    public function sources()
    {
        return $this->hasMany(ProductSource::class);
    }

    public function documents()
    {
        return $this->hasMany(KnowledgeDocument::class);
    }

    public function publishedDocuments()
    {
        return $this->hasMany(KnowledgeDocument::class)->where('status', KnowledgeDocument::STATUS_PUBLISHED);
    }

    /**
     * Owner tampilan: "Brand Name (Model)".
     */
    public function displayName(): string
    {
        return trim(collect([$this->brand, $this->name, $this->model])->filter()->join(' '));
    }

    /**
     * Identitas cukup jelas untuk auto-publish? Nama wajib + (model atau SKU).
     */
    public function hasClearIdentity(): bool
    {
        return filled($this->name) && (filled($this->model) || filled($this->sku));
    }
}
