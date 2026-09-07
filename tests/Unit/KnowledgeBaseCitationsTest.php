<?php

namespace Tests\Unit;

use App\Enums\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseDocument;
use App\Support\KnowledgeBaseCitations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Responses\Data\UrlCitation;
use Tests\TestCase;

class KnowledgeBaseCitationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_citation_to_document_source(): void
    {
        KnowledgeBaseDocument::create([
            'original_name' => 'sop.pdf',
            'category' => KnowledgeBaseCategory::Sop->value,
            'store_id' => 'store-test',
            'provider_file_id' => 'files/abc123',
            'mime' => 'application/pdf',
            'size' => 100,
            'status' => 'ready',
        ]);

        $sources = KnowledgeBaseCitations::resolve([
            new UrlCitation('files/abc123', 'SOP title'),
        ]);

        $this->assertSame([
            ['name' => 'sop.pdf', 'category' => 'SOP'],
        ], $sources);
    }

    public function test_resolves_citation_with_bare_file_id(): void
    {
        KnowledgeBaseDocument::create([
            'original_name' => 'proposal.docx',
            'category' => KnowledgeBaseCategory::Proposal->value,
            'store_id' => 'store-test',
            'provider_file_id' => 'abc123',
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'size' => 100,
            'status' => 'ready',
        ]);

        $sources = KnowledgeBaseCitations::resolve([
            new UrlCitation('files/abc123'),
        ]);

        $this->assertSame('proposal.docx', $sources[0]['name']);
        $this->assertSame('Proposal', $sources[0]['category']);
    }

    public function test_failed_documents_are_not_used_as_sources(): void
    {
        KnowledgeBaseDocument::create([
            'original_name' => 'gagal.pdf',
            'category' => KnowledgeBaseCategory::Reports->value,
            'store_id' => 'store-test',
            'provider_file_id' => 'files/failed1',
            'mime' => 'application/pdf',
            'size' => 100,
            'status' => 'failed',
        ]);

        $sources = KnowledgeBaseCitations::resolve([
            new UrlCitation('files/failed1', 'Gagal'),
        ]);

        $this->assertSame([], $sources);
    }

    public function test_unknown_citation_falls_back_to_title(): void
    {
        $sources = KnowledgeBaseCitations::resolve([
            new UrlCitation('https://external.example/doc', 'Judul Dokumen'),
        ]);

        $this->assertSame([
            ['name' => 'Judul Dokumen', 'category' => null],
        ], $sources);
    }

    public function test_empty_citations_return_empty_sources(): void
    {
        $this->assertSame([], KnowledgeBaseCitations::resolve([]));
        $this->assertSame([], KnowledgeBaseCitations::resolve(null));
    }
}
