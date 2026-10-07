<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductSource;
use App\Services\Knowledge\FirecrawlService;
use App\Services\Knowledge\ProductExtractor;
use App\Services\Knowledge\ProductKnowledgePipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Satu URL = satu job. Gagal di satu URL tidak menghentikan batch.
 */
class ImportProductSource implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $sourceId,
        public int $depth = 0,
    ) {}

    /**
     * Katalog: tanpa Product record (jangan gabung banyak model),
     * sebar satu source per halaman produk yang ditemukan.
     */
    protected function importAsCatalog(ProductSource $source, ProductKnowledgePipeline $pipeline, array $links): void
    {
        foreach ($links as $url) {
            if (ProductSource::where('url', $url)->exists()) {
                continue;
            }

            $child = ProductSource::create([
                'url' => $url,
                'source_type' => $pipeline->detectSourceType($url),
                'status' => ProductSource::STATUS_QUEUED,
                'batch_id' => $source->batch_id,
            ]);
            ImportProductSource::dispatch($child->id, 1);
        }

        $source->forceFill([
            'source_type' => ProductSource::TYPE_CATALOG,
            'status' => ProductSource::STATUS_SUCCESS,
            'last_fetched_at' => now(),
        ])->save();
    }

    public function handle(
        FirecrawlService $firecrawl,
        ProductExtractor $extractor,
        ProductKnowledgePipeline $pipeline,
    ): void {
        $source = ProductSource::find($this->sourceId);
        if (!$source || in_array($source->status, [ProductSource::STATUS_DUPLICATE], true)) {
            return;
        }

        $source->forceFill(['status' => ProductSource::STATUS_PROCESSING, 'error' => null])->save();

        try {
            $scraped = $firecrawl->scrape($source->url);
            $markdown = $pipeline->cleanContent($scraped['markdown']);

            // 1 URL bisa = banyak produk: temukan link produk se-host dulu.
            if ($this->depth === 0) {
                $links = $pipeline->discoverProductLinks($source->url, $scraped['links']);

                if (count($links) >= 2) {
                    $this->importAsCatalog($source, $pipeline, $links);

                    return;
                }

                // Tak ada link produk tapi konten tipis -> mungkin listing JS -> crawl.
                // Satu link = tetap anggap halaman produk (1 URL = 1 produk).
                if ($links === [] && mb_strlen($markdown) < config('knowledge.firecrawl.listing_threshold')) {
                    $crawlId = $firecrawl->crawlStart($source->url);
                    PollProductCrawl::dispatch($crawlId, (string) $source->batch_id, $source->id)->delay(now()->addMinutes(2));

                    return;
                }
            }

            $extracted = $extractor->extract($markdown);
            [$product, ] = $pipeline->matchOrCreateProduct($extracted);
            $pipeline->storeDocument($source, $product, $extracted, $markdown);

            $source->forceFill([
                'source_type' => $pipeline->detectSourceType($source->url),
                'status' => $product->hasClearIdentity()
                    ? ProductSource::STATUS_SUCCESS
                    : ProductSource::STATUS_NEEDS_REVIEW,
            ])->save();
        } catch (Throwable $e) {
            Log::warning('Import URL produk gagal', ['source_id' => $source->id, 'error' => $e->getMessage()]);
            $source->forceFill([
                'status' => ProductSource::STATUS_FAILED,
                'error' => mb_substr($e->getMessage(), 0, 500),
            ])->save();

            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
}
