<?php

namespace App\Enums;

enum ReturnCondition: string
{
    case Baik = 'baik';
    case RusakRingan = 'rusak_ringan';
    case RusakBerat = 'rusak_berat';
    case Hilang = 'hilang';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik',
            self::RusakRingan => 'Rusak Ringan',
            self::RusakBerat => 'Rusak Berat',
            self::Hilang => 'Hilang',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Baik => 'success',
            self::RusakRingan => 'warning',
            self::RusakBerat => 'danger',
            self::Hilang => 'danger',
        };
    }

    public function copyStatus(): BookCopyStatus
    {
        return match ($this) {
            self::Baik => BookCopyStatus::Tersedia,
            self::RusakRingan, self::RusakBerat => BookCopyStatus::Rusak,
            self::Hilang => BookCopyStatus::Hilang,
        };
    }

    public function copyCondition(): ?BookCondition
    {
        return match ($this) {
            self::Baik => BookCondition::Baik,
            self::RusakRingan => BookCondition::RusakRingan,
            self::RusakBerat => BookCondition::RusakBerat,
            self::Hilang => null,
        };
    }
}
