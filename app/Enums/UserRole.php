<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Technician = 'technician';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Usuario',
            self::Technician => 'Técnico',
            self::Admin => 'Administrador',
        };
    }
}
