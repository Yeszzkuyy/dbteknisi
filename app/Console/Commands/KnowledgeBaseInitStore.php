<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Ai\Stores;
use Throwable;

class KnowledgeBaseInitStore extends Command
{
    protected $signature = 'knowledge-base:init-store';

    protected $description = 'Buat vector store Knowledge Base di Gemini dan simpan store ID ke .env';

    public function handle(): int
    {
        $storeId = config('ai.knowledge_base.store_id');

        if (filled($storeId)) {
            $this->warn("KNOWLEDGE_BASE_STORE_ID sudah terisi ({$storeId}). Tidak ada perubahan.");

            return self::SUCCESS;
        }

        if (blank(config('ai.providers.gemini.key'))) {
            $this->error('GEMINI_API_KEY belum dikonfigurasi di .env.');

            return self::FAILURE;
        }

        try {
            $store = Stores::create(config('ai.knowledge_base.store_name'));
        } catch (Throwable $e) {
            $this->error('Gagal membuat vector store: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->writeEnvValue('KNOWLEDGE_BASE_STORE_ID', $store->id);

        $this->info('Vector store dibuat: '.$store->id);
        $this->info('Jalankan `php artisan config:clear` jika sebelumnya pernah menjalankan `config:cache`.');

        return self::SUCCESS;
    }

    protected function writeEnvValue(string $key, string $value): void
    {
        $path = app()->environmentFilePath();
        $contents = (string) file_get_contents($path);

        if (preg_match("/^{$key}=.*$/m", $contents)) {
            $contents = (string) preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $contents);
        } else {
            $contents .= "\n{$key}={$value}\n";
        }

        file_put_contents($path, $contents);
    }
}
