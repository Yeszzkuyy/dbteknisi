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

            // Halaman listing/katalog tipis -> crawl anaknya (maks 1 tingkat).
            if ($this->depth === 0 && mb_strlen($markdown) < config('knowledge.firecrawl.listing_threshold') && $scraped['links']) {
                $crawlId = $firecrawl->crawlStart($source->url);
                PollProductCrawl::dispatch($crawlId, (string) $source->batch_id, $source->id)->delay(now()->addMinutes(2));

                return;
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
