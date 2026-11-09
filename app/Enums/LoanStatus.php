<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Dipinjam = 'dipinjam';
    case Dikembalikan = 'dikembalikan';

    public function label(): string
    {
        return match ($this) {
            self::Dipinjam => 'Dipinjam',
            self::Dikembalikan => 'Dikembalikan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Dipinjam => 'primary',
            self::Dikembalikan => 'success',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Dipinjam;
    }
}
