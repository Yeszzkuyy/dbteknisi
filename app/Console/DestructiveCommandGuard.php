<?php

namespace App\Console;

use Illuminate\Support\Facades\Process;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Menahan perintah database destruktif (migrate:fresh, migrate:refresh,
 * migrate:reset, db:wipe): DB dummy (test/sqlite-memory) lolos diam-diam,
 * selain itu wajib backup otomatis + ketik nama database persis.
 * Input kosong/salah/non-interaktif = ditolak sebelum aksi apa pun jalan.
 */
class DestructiveCommandGuard
{
    public const COMMANDS = [
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'db:wipe',
    ];

    public function shouldGuard(string $command, string $driver, ?string $database, bool $runningTests): bool
    {
        if (! in_array($command, self::COMMANDS, true)) {
            return false;
        }
        if ($runningTests) {
            return false;
        }
        if ($driver === 'sqlite' && $this->isDummyDatabase($database)) {
            return false;
        }

        return true;
    }

    public function handle(string $commandName, InputInterface $input, OutputInterface $output, string $connection, string $driver, ?string $database): void
    {
        $io = new SymfonyStyle($input, $output);
        $label = $database ?: $connection;
        $io->warning("Perintah destruktif '{$commandName}' ditahan guardrail.");
        $this->backup($io, $connection, $driver, $database);

        $answer = $io->ask("Ketik nama database '{$label}' persis untuk lanjut (Ctrl-C = batal)");
        if (! is_string($answer) || trim($answer) !== (string) $label) {
            throw new RuntimeException('Dibatalkan: konfirmasi nama database tidak cocok.');
        }
        $io->success('Konfirmasi cocok. Melanjutkan...');
    }

    private function isDummyDatabase(?string $database): bool
    {
        if ($database === null || $database === ':memory:') {
            return true;
        }
        $base = strtolower(basename($database));

        return str_contains($base, 'test');
    }

    private function backup(SymfonyStyle $io, string $connection, string $driver, ?string $database): void
    {
        if ($driver !== 'pgsql' || ! $database) {
            $io->warning("Backup otomatis dilewati (driver '{$driver}' di luar cakupan pg_dump).");
            return;
        }
        $cfg = config("database.connections.{$connection}", []);
        $dir = base_path('backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = $dir.'/pre-destructive-'.date('Ymd-His').'-'.preg_replace('/[^A-Za-z0-9_-]/', '_', $database).'.dump';
        $result = Process::env(['PGPASSWORD' => $cfg['password'] ?? ''])->run([
            'pg_dump',
            '-h', (string) ($cfg['host'] ?? '127.0.0.1'),
            '-p', (string) ($cfg['port'] ?? '5432'),
            '-U', (string) ($cfg['username'] ?? ''),
            '-d', $database,
            '-Fc',
            '-f', $file,
        ]);
        if (! $result->successful()) {
            throw new RuntimeException('Backup otomatis gagal, perintah dibatalkan: '.trim($result->errorOutput()));
        }
        $io->info("Backup tersimpan: {$file}");
    }
}
