<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBaseDocument;
use Illuminate\Console\Command;
use Laravel\Ai\Stores;
use Throwable;

class KnowledgeBaseSyncStatus extends Command
{
    protected $signature = 'knowledge-base:sync-status';

    protected $description = 'Sinkronkan status indexing dokumen Knowledge Base dari penyedia';

    public function handle(): int
    {
        $storeId = config('ai.knowledge_base.store_id');

        if (blank($storeId)) {
            $this->error('KNOWLEDGE_BASE_STORE_ID belum dikonfigurasi. Jalankan `knowledge-base:init-store` dulu.');

            return self::FAILURE;
        }

        try {
            $counts = Stores::get($storeId)->fileCounts;
        } catch (Throwable $e) {
            $this->error('Gagal mengambil status store: '.$e->getMessage());

            return self::FAILURE;
        }

        $updated = 0;

        if ($counts->failed > 0) {
            $updated += KnowledgeBaseDocument::where('store_id', $storeId)
                ->where('status', 'indexing')
                ->update(['status' => 'failed']);
        }

        if ($counts->failed === 0 && $counts->pending === 0) {
            $updated += KnowledgeBaseDocument::where('store_id', $storeId)
                ->where('status', 'indexing')
                ->update(['status' => 'ready']);
        }

        $this->info("Status store: completed={$counts->completed}, pending={$counts->pending}, failed={$counts->failed}.");
        $this->info("{$updated} dokumen diperbarui.");

        return self::SUCCESS;
    }
}
