<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum EquipmentCategory: string
{
    case Laptop = 'laptop';
    case Phone = 'phone';
    case Monitor = 'monitor';

    public function label(): string
    {
        return match ($this) {
            self::Laptop => 'Laptop',
            self::Phone => 'Celular',
            self::Monitor => 'Monitor',
        };
    }

    public function importLabel(): string
    {
        return match ($this) {
            self::Laptop => 'Equipo',
            self::Phone => 'Celular',
            self::Monitor => 'Monitor',
        };
    }

    public static function fromImportValue(mixed $value): ?self
    {
        $normalized = Str::lower(Str::ascii(trim((string) $value)));

        return match ($normalized) {
            'equipo', 'equipos', 'laptop', 'laptops', 'computadora', 'computadoras' => self::Laptop,
            'celular', 'celulares', 'telefono', 'telefonos', 'phone', 'phones' => self::Phone,
            'monitor', 'monitores' => self::Monitor,
            default => null,
        };
    }
}
