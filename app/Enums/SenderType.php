<?php

namespace App\Enums;

enum SenderType: string
{
    case Pelanggan = 'Pelanggan';
    case Staf = 'Staf';
    case Sistem = 'Sistem';

    public function label(): string
    {
        return $this->value;
    }
}
