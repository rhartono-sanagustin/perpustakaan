<?php

namespace App\Enums;

enum AnnouncementAudience: string
{
    case Semua = 'semua';
    case Guru = 'guru';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::Semua => 'Semua Pengguna',
            self::Guru => 'Guru',
            self::Siswa => 'Siswa',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Semua => 'primary',
            self::Guru => 'info',
            self::Siswa => 'success',
        };
    }
}
