<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Open = 'Open';
    case OnProgress = 'On Progress';
    case Done = 'Done';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * Warna badge Tailwind untuk masing-masing status, dipakai di Blade
     * biar warnanya konsisten di semua view (index, show, dsb).
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open       => 'bg-blue-100 text-blue-800 border border-blue-200',
            self::OnProgress => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            self::Done       => 'bg-green-100 text-green-800 border border-green-200',
            self::Cancelled  => 'bg-red-100 text-red-800 border border-red-200',
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
