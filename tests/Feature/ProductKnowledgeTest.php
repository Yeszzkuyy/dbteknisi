<?php

namespace Tests\Feature;

use App\Ai\Tools\GetProductKnowledge;
use App\Jobs\ImportProductSource;
use App\Models\Product;
use App\Models\ProductSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductKnowledgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Kunci dummy: HTTP difake, yang penting lolos cek konfigurasi.
        config()->set('knowledge.firecrawl.key', 'test-key');
        config()->set('ai.providers.openrouter.key', 'test-key');
    }

    private function admin(): User
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $u = User::factory()->create();
        $u->assignRole('super-admin');

        return $u;
    }

    private function scrapeFake(&$markdown, array $links = []): void
    {
        Http::fake([
            'api.firecrawl.dev/v1/scrape' => function () use (&$markdown, $links) {
                // Padding > listing_threshold agar jalur halaman-produk, bukan crawl.
                $body = $markdown."\n\n".str_repeat('Konten resmi produk. ', 40);
                return Http::response([
                    'success' => true,
                    'data' => ['markdown' => $body, 'metadata' => [], 'links' => $links],
                ]);
            },
            'openrouter.ai/*' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'brand' => 'Yeastar', 'name' => 'P-Series', 'model' => 'P560',
                        'sku' => null, 'category' => 'IP PBX',
                        'description' => 'IP PBX untuk usaha.',
                        'specifications' => 'Up to 100 users.',
                    ])],
                ]],
            ]),
        ]);
    }

    private function importUrls(array $urls, bool $sync = true): array
    {
        $sources = [];
        foreach ($urls as $url) {
            $source = ProductSource::create([
                'url' => $url,
                'source_type' => ProductSource::TYPE_PRODUCT_PAGE,
                'status' => ProductSource::STATUS_QUEUED,
                'batch_id' => 'batch-1',
            ]);
            if ($sync) {
                // Gagal sync = status FAILED sudah tercatat job; lanjut URL berikut.
                try {
                    ImportProductSource::dispatchSync($source->id);
                } catch (\Throwable $e) {
                    // ditelan sengaja
                }
            }
            $sources[] = $source->fresh();
        }

        return $sources;
    }

    public function test_admin_gate(): void
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $user = User::factory()->create();
        $user->assignRole('technician');

        $this->actingAs($user)->get(route('product-knowledge.index'))->assertForbidden();
        $this->actingAs($user)->post(route('product-knowledge.import'), ['urls' => 'https://x.com/a'])
            ->assertForbidden();
    }

    public function test_import_single_url_success(): void
    {
        $this->actingAs($this->admin());
        $md = "# Yeastar P560\nIP PBX untuk usaha. Up to 100 users.";
        $this->scrapeFake($md);

        [$source] = $this->importUrls(['https://vendor.com/product/1']);

        $this->assertEquals(ProductSource::STATUS_PUBLISHED, $source->status);
        $product = $source->fresh()->product;
        $this->assertNotNull($product);
        $this->assertEquals('Yeastar', $product->brand);
        $this->assertEquals('P560', $product->model);
        $this->assertEquals(1, $product->documents()->count());
        $this->assertGreaterThanOrEqual(1, $product->documents()->first()->chunks()->count());
    }

    public function test_batch_continues_when_one_url_fails(): void
    {
        $this->actingAs($this->admin());
        Http::fake([
            'api.firecrawl.dev/v1/scrape' => function ($request) {
                $url = $request->data()['url'] ?? '';
                if (str_contains($url, '/gagal')) {
                    return Http::response(['error' => 'boom'], 500);
                }

                return Http::response(['success' => true, 'data' => ['markdown' => 'ok '.str_repeat('konten resmi. ', 60), 'metadata' => [], 'links' => []]]);
            },
            'openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
                'brand' => 'B', 'name' => 'N', 'model' => 'M', 'sku' => null,
                'category' => null, 'description' => null, 'specifications' => null,
            ])]]]]),
        ]);

        [$ok, $fail] = $this->importUrls(['https://vendor.com/ok', 'https://vendor.com/gagal']);

        $this->assertEquals(ProductSource::STATUS_FAILED, $fail->status);
        $this->assertNotNull($fail->error);
        $this->assertEquals(ProductSource::STATUS_PUBLISHED, $ok->status);
    }

    public function test_import_dispatches_one_job_per_url(): void
    {
        $this->actingAs($this->admin());
        Bus::fake();

        $this->post(route('product-knowledge.import'), [
            'urls' => "https://a.com/1\nhttps://a.com/2\nhttps://a.com/1\n",
        ])->assertRedirect();

        Bus::assertDispatchedTimes(ImportProductSource::class, 2);
        $this->assertEquals(2, ProductSource::count());
    }

    public function test_double_paste_same_batch_processed_once(): void
    {
        $this->actingAs($this->admin());
        $md = 'konten ganda';
        $this->scrapeFake($md);

        // Antrean sync: job langsung jalan saat POST.
        $this->post(route('product-knowledge.import'), [
            'urls' => "https://vendor.com/ganda\nhttps://vendor.com/ganda\n",
        ])->assertRedirect();

        // Satu baris, diproses normal (bukan duplicate).
        $this->assertEquals(1, ProductSource::count());
        $source = ProductSource::first();
        $this->assertNotEquals(ProductSource::STATUS_DUPLICATE, $source->status);
        $this->assertNotNull($source->product_id);
    }

    public function test_reimport_processed_url_shows_duplicate_without_touching(): void
    {
        $this->actingAs($this->admin());
        $md = 'konten';
        $this->scrapeFake($md);

        $this->importUrls(['https://vendor.com/dup']);
        $originalStatus = ProductSource::first()->status;
        $this->assertEquals(1, ProductSource::count());

        $this->post(route('product-knowledge.import'), ['urls' => 'https://vendor.com/dup'])
            ->assertRedirect()
            ->assertSessionHas('duplicateUrls', ['https://vendor.com/dup']);

        // Baris asli tak tersentuh.
        $this->assertEquals(1, ProductSource::count());
        $this->assertEquals($originalStatus, ProductSource::first()->status);
    }

    public function test_reimport_failed_url_retries(): void
    {
        $this->actingAs($this->admin());
        $failed = ProductSource::create([
            'url' => 'https://vendor.com/gagal-dulu',
            'status' => ProductSource::STATUS_FAILED,
            'error' => 'boom',
            'batch_id' => 'lama',
        ]);

        $md = 'konten pulih';
        $this->scrapeFake($md);

        $this->post(route('product-knowledge.import'), ['urls' => 'https://vendor.com/gagal-dulu'])
            ->assertRedirect();

        // Baris sama dipakai ulang + diproses (sync), bukan duplicate.
        $this->assertEquals(1, ProductSource::where('url', 'https://vendor.com/gagal-dulu')->count());
        $this->assertNotEquals(ProductSource::STATUS_DUPLICATE, $failed->fresh()->status);
        $this->assertNotNull($failed->fresh()->product_id);
    }

    public function test_same_product_different_source_appends(): void
    {
        $this->actingAs($this->admin());
        $md = 'konten';
        $this->scrapeFake($md);

        $this->importUrls(['https://vendor.com/product/1']);
        $this->importUrls(['https://vendor.com/datasheet/1.pdf']);

        $this->assertEquals(1, Product::count());
        $this->assertEquals(2, Product::first()->sources()->count());
    }

    public function test_missing_metadata_needs_review(): void
    {
        $this->actingAs($this->admin());
        Http::fake([
            'api.firecrawl.dev/v1/scrape' => Http::response([
                'success' => true, 'data' => ['markdown' => 'halo dunia '.str_repeat('konten resmi. ', 60), 'metadata' => [], 'links' => []],
            ]),
            'openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
                'brand' => null, 'name' => null, 'model' => null, 'sku' => null,
                'category' => null, 'description' => null, 'specifications' => null,
            ])]]]]),
        ]);

        [$source] = $this->importUrls(['https://vendor.com/samar']);

        $this->assertEquals(ProductSource::STATUS_NEEDS_REVIEW, $source->status);
    }

    public function test_valid_auto_publishes_and_retrieval(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $md = 'konten';
        $this->scrapeFake($md);

        [$source] = $this->importUrls(['https://vendor.com/product/9']);
        $product = $source->fresh()->product;

        // Tanpa klik apa pun: produk + dokumen + source langsung PUBLISHED.
        $this->assertEquals(Product::STATUS_PUBLISHED, $product->status);
        $this->assertEquals('published', $product->documents()->first()->status);

        $tool = new GetProductKnowledge($admin);
        $found = (string) $tool->handle(new \Laravel\Ai\Tools\Request(['query' => 'P560']));
        $this->assertStringContainsString('P560', $found);
        $this->assertStringContainsString('https:\/\/vendor.com\/product\/9', $found);
    }

    public function test_needs_review_fix_then_publish(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        Http::fake([
            'api.firecrawl.dev/v1/scrape' => Http::response([
                'success' => true,
                'data' => ['markdown' => 'samar '.str_repeat('konten resmi. ', 60), 'metadata' => [], 'links' => []],
            ]),
            'openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
                'brand' => null, 'name' => null, 'model' => null, 'sku' => null,
                'category' => null, 'description' => null, 'specifications' => null,
            ])]]]]),
        ]);

        [$source] = $this->importUrls(['https://vendor.com/samar-2']);
        $product = $source->fresh()->product;

        // Alasan tercatat, belum publish.
        $this->assertEquals(ProductSource::STATUS_NEEDS_REVIEW, $source->fresh()->status);
        $this->assertStringContainsString('Perlu review', (string) $source->fresh()->error);
        $this->assertNotEquals(Product::STATUS_PUBLISHED, $product->status);

        // Admin betulkan data lalu publish manual.
        $this->patch(route('product-knowledge.update', $product), [
            'brand' => 'Yeastar', 'name' => 'P-Series', 'model' => 'P520',
        ])->assertRedirect();
        $this->patch(route('product-knowledge.publish', $product))->assertRedirect();
        $this->assertEquals(Product::STATUS_PUBLISHED, $product->fresh()->status);

        $tool = new GetProductKnowledge($admin);
        $found = (string) $tool->handle(new \Laravel\Ai\Tools\Request(['query' => 'P520']));
        $this->assertStringContainsString('P520', $found);
    }

    public function test_invalid_url_rejected_by_validation(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('product-knowledge.import'), ['urls' => 'bukan-url'])
            ->assertSessionHasErrors('urls');
        $this->assertEquals(0, ProductSource::count());
    }

    public function test_sales_cannot_manage(): void
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('product-knowledge.index'))->assertForbidden();
        $this->actingAs($sales)->post(route('product-knowledge.import'), ['urls' => 'https://x.com/a'])
            ->assertForbidden();
    }

    public function test_rejected_excluded_from_retrieval(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $md = 'konten';
        $this->scrapeFake($md);

        [$source] = $this->importUrls(['https://vendor.com/product/8']);
        $product = $source->fresh()->product;

        $this->assertEquals(Product::STATUS_PUBLISHED, $product->fresh()->status);
        $this->patch(route('product-knowledge.reject', $product))->assertRedirect();

        $tool = new GetProductKnowledge($admin);
        $this->assertStringContainsString('Tidak ada', (string) $tool->handle(new \Laravel\Ai\Tools\Request(['query' => 'P560'])));
    }

    public function test_refetch_unchanged_no_new_version(): void
    {
        $this->actingAs($this->admin());
        $md = 'konten sama';
        $this->scrapeFake($md);

        [$source] = $this->importUrls(['https://vendor.com/product/7']);
        $this->assertEquals(1, $source->fresh()->product->documents()->count());

        $productsBefore = Product::count();

        // Fetch ulang isi sama -> tanpa dokumen baru, tanpa produk yatim.
        ImportProductSource::dispatchSync($source->fresh()->id);
        $this->assertEquals($productsBefore, Product::count());
        $this->assertEquals(1, $source->fresh()->product->documents()->count());
        $this->assertEquals(ProductSource::STATUS_PUBLISHED, $source->fresh()->status);
    }

    public function test_refetch_changed_creates_new_version(): void
    {
        $this->actingAs($this->admin());
        $md = 'konten v1';
        $this->scrapeFake($md);

        [$source] = $this->importUrls(['https://vendor.com/product/6']);
        $product = $source->fresh()->product;

        $md = 'konten v1 plus tambahan spesifikasi baru';
        $this->scrapeFake($md);
        $this->post(route('product-knowledge.refetch', $source))->assertRedirect();
        ImportProductSource::dispatchSync($source->fresh()->id);

        $docs = $product->documents()->orderBy('version')->get();
        $this->assertEquals(2, $docs->count());
        $this->assertEquals([1, 2], $docs->pluck('version')->all());
        // Versi lama tetap tercatat.
        $this->assertTrue($docs->first()->chunks()->exists());
    }
}
