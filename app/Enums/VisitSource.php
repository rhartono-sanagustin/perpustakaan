<?php

namespace App\Enums;

enum VisitSource: string
{
    case Admin = 'admin';
    case Mandiri = 'mandiri';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Mandiri => 'Mandiri',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Admin => 'secondary',
            self::Mandiri => 'info',
        };
    }
}
