<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'Admin';
    case CustomerService = 'Customer Service';
    case Gudang = 'Gudang';
    case Manajemen = 'Manajemen';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * Semua peran yang diakui sistem.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
