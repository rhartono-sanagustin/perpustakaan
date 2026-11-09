<?php

namespace App\Enums;

enum LoanRequestStatus: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dipinjam = 'dipinjam';
    case Dikembalikan = 'dikembalikan';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Diproses',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
            self::Dipinjam => 'Dipinjam',
            self::Dikembalikan => 'Dikembalikan',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Disetujui => 'info',
            self::Ditolak => 'danger',
            self::Dipinjam => 'primary',
            self::Dikembalikan => 'success',
            self::Dibatalkan => 'secondary',
        };
    }

    public function isPending(): bool
    {
        return $this === self::Menunggu;
    }

    public function isApproved(): bool
    {
        return $this === self::Disetujui;
    }

    public function canBeCancelled(): bool
    {
        return $this === self::Menunggu || $this === self::Disetujui;
    }
}
