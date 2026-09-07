<?php

namespace App\Support;

use App\Models\KnowledgeBaseDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KnowledgeBaseCitations
{
    /**
     * Resolve provider citations into human-readable document sources.
     *
     * @param  iterable<mixed>|null  $citations
     * @return array<int, array{name: string, category: ?string}>
     */
    public static function resolve(?iterable $citations): array
    {
        if (! $citations) {
            return [];
        }

        return (new Collection($citations))
            ->map(fn ($citation) => static::resolveOne($citation))
            ->filter()
            ->unique(fn ($source) => $source['name'])
            ->values()
            ->all();
    }

    protected static function resolveOne(mixed $citation): ?array
    {
        $url = $citation->url ?? null;
        $fileId = static::extractFileId($url);

        if ($fileId) {
            $document = KnowledgeBaseDocument::query()
                ->where(function ($query) use ($fileId) {
                    $query->where('provider_file_id', $fileId)
                        ->orWhere('provider_file_id', 'files/'.$fileId);
                })
                ->first();

            if ($document) {
                if ($document->isFailed()) {
                    return null;
                }

                return [
                    'name' => $document->original_name,
                    'category' => $document->category?->label(),
                ];
            }
        }

        if (! blank($citation->title ?? null)) {
            return ['name' => $citation->title, 'category' => null];
        }

        return null;
    }

    protected static function extractFileId(?string $url): ?string
    {
        if (! Str::contains($url ?? '', 'files/')) {
            return null;
        }

        $candidate = Str::afterLast($url, 'files/');

        return trim($candidate) !== '' ? $candidate : null;
    }
}
