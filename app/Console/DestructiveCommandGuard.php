<?php

namespace App\Console;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Menolak permanen perintah database destruktif (migrate:fresh, migrate:refresh,
 * migrate:reset, db:wipe, db:drop): default-deny tanpa bypass.
 * DB dummy (test/sqlite-memory) dan test suite lolos diam-diam via shouldGuard(),
 * selain itu handle() langsung menolak sebelum aksi apa pun jalan.
 */
class DestructiveCommandGuard
{
    public const COMMANDS = [
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'db:wipe',
        'db:drop',
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
        throw new RuntimeException(
            "DITOLAK oleh safety guard: perintah '{$commandName}' bersifat destruktif dan diblokir permanen. Gunakan 'migrate' biasa."
        );
    }

    private function isDummyDatabase(?string $database): bool
    {
        if ($database === null || $database === ':memory:') {
            return true;
        }
        $base = strtolower(basename($database));

        return str_contains($base, 'test');
    }
}
