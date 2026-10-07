<?php

namespace App\Ai\Tools;

use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProductKnowledge implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Mencari spesifikasi/informasi produk di Product Knowledge Base (hanya data PUBLISHED dari sumber resmi). '
            .'Parameter: query (wajib), brand/model opsional sebagai filter. Read-only.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Kata kunci produk/spesifikasi'),
            'brand' => $schema->string()->nullable()->description('Filter brand'),
            'model' => $schema->string()->nullable()->description('Filter model'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $query = trim((string) ($request['query'] ?? ''));
        if ($query === '') {
            return 'Query kosong.';
        }

        $brand = trim((string) ($request['brand'] ?? ''));
        $model = trim((string) ($request['model'] ?? ''));

        $chunkQuery = KnowledgeChunk::query()
            ->join('knowledge_documents', 'knowledge_documents.id', '=', 'knowledge_chunks.document_id')
            ->join('products', 'products.id', '=', 'knowledge_documents.product_id')
            ->where('knowledge_documents.status', KnowledgeDocument::STATUS_PUBLISHED)
            ->where('products.status', Product::STATUS_PUBLISHED);
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            $chunkQuery->whereRaw("to_tsvector('simple', knowledge_chunks.content) @@ plainto_tsquery('simple', ?)", [$query]);
        } else {
            $chunkQuery->where('knowledge_chunks.content', 'like', '%' . $query . '%');
        }

        if ($brand !== '') {
            $chunkQuery->whereRaw('LOWER(products.brand) = ?', [mb_strtolower($brand)]);
        }
        if ($model !== '') {
            $chunkQuery->whereRaw('LOWER(products.model) = ?', [mb_strtolower($model)]);
        }

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            $chunkQuery->orderByRaw("ts_rank(to_tsvector('simple', knowledge_chunks.content), plainto_tsquery('simple', ?)) DESC", [$query]);
        } else {
            $chunkQuery->latest('knowledge_chunks.id');
        }

        $chunks = $chunkQuery
            ->limit(6)
            ->get([
                'knowledge_chunks.content',
                'products.brand', 'products.name as product_name', 'products.model',
                'products.sku', 'products.category',
                'knowledge_documents.source_url', 'knowledge_documents.version',
            ]);

        if ($chunks->isEmpty()) {
            return 'Tidak ada knowledge produk PUBLISHED yang cocok. Katakan data tidak tersedia, jangan mengarang spesifikasi.';
        }

        return json_encode([
            'results' => $chunks->map(fn ($c) => [
                'brand' => $c->brand,
                'product' => $c->product_name,
                'model' => $c->model,
                'sku' => $c->sku,
                'category' => $c->category,
                'snippet' => mb_substr((string) $c->content, 0, 1200),
                'source_url' => $c->source_url,
                'version' => $c->version,
            ])->all(),
        ], JSON_UNESCAPED_UNICODE);
    }
}
