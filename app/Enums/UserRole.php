<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Teknisi = 'teknisi';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Teknisi => 'Teknisi',
            self::User => 'User',
        };
    }
}
