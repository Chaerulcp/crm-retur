<?php

namespace App\Enums;

enum ItemCondition: string
{
    case BelumDiterima = 'Belum Diterima';
    case Layak = 'Layak';
    case TidakLayak = 'Tidak Layak';

    public function label(): string
    {
        return $this->value;
    }
}
