<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Guru = 'guru';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Guru => 'Guru',
            self::Siswa => 'Siswa',
        };
    }

    public function isBorrower(): bool
    {
        return $this === self::Guru || $this === self::Siswa;
    }
}
