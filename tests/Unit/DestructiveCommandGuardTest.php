<?php

namespace Tests\Unit;

use App\Console\DestructiveCommandGuard;
use PHPUnit\Framework\TestCase;

class DestructiveCommandGuardTest extends TestCase
{
    private DestructiveCommandGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guard = new DestructiveCommandGuard();
    }

    public function test_perintah_aman_tidak_ditahan(): void
    {
        $this->assertFalse($this->guard->shouldGuard('migrate', 'pgsql', 'db_teknisi', false));
        $this->assertFalse($this->guard->shouldGuard('migrate:status', 'pgsql', 'db_teknisi', false));
        $this->assertFalse($this->guard->shouldGuard('db:seed', 'pgsql', 'db_teknisi', false));
    }

    public function test_perintah_destruktif_di_db_asli_ditahan(): void
    {
        foreach (DestructiveCommandGuard::COMMANDS as $command) {
            $this->assertTrue(
                $this->guard->shouldGuard($command, 'pgsql', 'db_teknisi', false),
                "perintah {$command} harus ditahan"
            );
        }
        $this->assertTrue($this->guard->shouldGuard('db:wipe', 'mysql', 'prod', false));
    }

    public function test_db_dummy_lolos_diam_diam(): void
    {
        $this->assertFalse($this->guard->shouldGuard('migrate:fresh', 'pgsql', 'db_teknisi', true));
        $this->assertFalse($this->guard->shouldGuard('migrate:fresh', 'sqlite', ':memory:', false));
        $this->assertFalse($this->guard->shouldGuard('migrate:fresh', 'sqlite', 'database/testing.sqlite', false));
    }

    public function test_sqlite_non_dummy_tetap_ditahan(): void
    {
        $this->assertTrue($this->guard->shouldGuard('migrate:fresh', 'sqlite', 'database/database.sqlite', false));
    }
}
