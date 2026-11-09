<?php

namespace App\Enums;

enum BookCopyStatus: string
{
    case Tersedia = 'tersedia';
    case Dipinjam = 'dipinjam';
    case Rusak = 'rusak';
    case Hilang = 'hilang';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Dipinjam => 'Dipinjam',
            self::Rusak => 'Rusak',
            self::Hilang => 'Hilang',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Tersedia => 'success',
            self::Dipinjam => 'primary',
            self::Rusak => 'warning',
            self::Hilang => 'danger',
            self::Nonaktif => 'secondary',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::Tersedia;
    }
}
