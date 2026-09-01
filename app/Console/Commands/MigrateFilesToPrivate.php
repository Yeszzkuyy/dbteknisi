<?php

namespace App\Console\Commands;

use App\Models\LeadDocument;
use App\Models\Payment;
use App\Models\ProjectDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToPrivate extends Command
{
    protected $signature = 'files:migrate-private
        {--purge-public : Hapus file dari disk public setelah berhasil disalin ke private}';

    protected $description = 'Salin dokumen sensitif (lead, project, bukti pembayaran) dari disk public ke disk private';

    private const SOURCES = [
        LeadDocument::class => 'file_path',
        ProjectDocument::class => 'file_path',
        Payment::class => 'proof_file',
    ];

    public function handle(): int
    {
        $copied = 0;
        $skipped = 0;
        $missing = 0;

        foreach (self::SOURCES as $model => $column) {
            $this->line("Memeriksa {$model}...");

            foreach ($model::whereNotNull($column)->cursor() as $record) {
                $path = $record->{$column};

                if (Storage::disk('private')->exists($path)) {
                    $skipped++;
                    continue;
                }

                if (!Storage::disk('public')->exists($path)) {
                    $missing++;
                    $this->warn("  file tidak ada di public: {$path}");
                    continue;
                }

                Storage::disk('private')->makeDirectory(dirname($path));

                if (Storage::disk('private')->writeStream(
                    $path,
                    Storage::disk('public')->readStream($path)
                )) {
                    $copied++;
                    $this->line("  disalin: {$path}");

                    if ($this->option('purge-public')) {
                        Storage::disk('public')->delete($path);
                        $this->line("  -> dihapus dari public: {$path}");
                    }
                } else {
                    $this->error("  gagal menyalin: {$path}");
                }
            }
        }

        $this->newLine();
        $this->info("Selesai. Disalin: {$copied}, sudah ada di private: {$skipped}, tidak ditemukan: {$missing}.");

        if ($missing > 0) {
            $this->warn('Ada file yang tidak ditemukan di public — periksa daftar di atas sebelum purge.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
