<?php

namespace App\Services\Knowledge;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Firecrawl REST API, hanya dari backend. Key tidak pernah ke frontend.
 */
class FirecrawlService
{
    public function configured(): bool
    {
        return filled(config('knowledge.firecrawl.key'));
    }

    protected function client()
    {
        if (!$this->configured()) {
            throw new RuntimeException('FIRECRAWL_API_KEY belum dikonfigurasi.');
        }

        return Http::baseUrl(rtrim((string) config('knowledge.firecrawl.base_url'), '/'))
            ->withToken((string) config('knowledge.firecrawl.key'))
            ->timeout((int) config('knowledge.firecrawl.timeout'))
            ->acceptJson();
    }

    /**
     * @return array{markdown:string,metadata:array,links:array}
     */
    public function scrape(string $url): array
    {
        $res = $this->client()->post('/v1/scrape', [
            'url' => $url,
            'onlyMainContent' => true,
            'formats' => ['markdown'],
        ]);

        $this->throwOnError($res, 'scrape');
        $data = (array) ($res->json('data') ?? []);

        return [
            'markdown' => (string) ($data['markdown'] ?? ''),
            'metadata' => (array) ($data['metadata'] ?? []),
            'links' => array_values(array_filter((array) ($data['links'] ?? []))),
        ];
    }

    /**
     * Ekstraksi terstruktur (LLM Firecrawl, prompt dikunci dari konten).
     * @return string job id
     */
    public function extractStart(array $urls): string
    {
        $res = $this->client()->post('/v1/extract', [
            'urls' => array_values($urls),
            'prompt' => 'Extract official product facts ONLY from the visible page content. '
                .'Never invent brand, name, model, SKU, category, description, or specifications. '
                .'If a field is unclear or missing, return null for it.',
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'brand' => ['type' => ['string', 'null']],
                    'name' => ['type' => ['string', 'null']],
                    'model' => ['type' => ['string', 'null']],
                    'sku' => ['type' => ['string', 'null']],
                    'category' => ['type' => ['string', 'null']],
                    'description' => ['type' => ['string', 'null']],
                    'specifications' => ['type' => ['string', 'null']],
                ],
            ],
        ]);

        $this->throwOnError($res, 'extract');
        $id = (string) $res->json('id');

        if (!$id) {
            throw new RuntimeException('Firecrawl extract: job id kosong.');
        }

        return $id;
    }

    public function extractStatus(string $jobId): array
    {
        $res = $this->client()->get('/v1/extract/'.$jobId);
        $this->throwOnError($res, 'extract-status');

        return (array) $res->json();
    }

    /**
     * @return string crawl job id
     */
    public function crawlStart(string $url, ?int $limit = null): string
    {
        $res = $this->client()->post('/v1/crawl', [
            'url' => $url,
            'limit' => $limit ?? (int) config('knowledge.firecrawl.crawl_limit'),
            'scrapeOptions' => ['onlyMainContent' => true, 'formats' => ['markdown']],
        ]);

        $this->throwOnError($res, 'crawl');
        $id = (string) ($res->json('id') ?? $res->json('jobId'));

        if (!$id) {
            throw new RuntimeException('Firecrawl crawl: job id kosong.');
        }

        return $id;
    }

    public function crawlStatus(string $jobId): array
    {
        $res = $this->client()->get('/v1/crawl/'.$jobId);
        $this->throwOnError($res, 'crawl-status');

        return (array) $res->json();
    }

    protected function throwOnError(Response $res, string $action): void
    {
        if ($res->successful()) {
            return;
        }

        $message = $res->json('error')
            ?? $res->json('message')
            ?? 'HTTP '.$res->status();

        throw new RuntimeException("Firecrawl {$action} gagal: ".substr((string) $message, 0, 300));
    }
}
