<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class OnlineStatus
{
    private const TTL_SECONDS = 150;

    public static function touch(int $userId): void
    {
        Cache::put(self::key($userId), time(), self::TTL_SECONDS);
    }

    public static function isOnline(int $userId, ?int $now = null): bool
    {
        $last = (int) Cache::get(self::key($userId), 0);
        return $last >= (($now ?? time()) - 120);
    }

    public static function forget(int $userId): void
    {
        Cache::forget(self::key($userId));
    }

    private static function key(int $userId): string
    {
        return "teknisi.online.{$userId}";
    }
}
