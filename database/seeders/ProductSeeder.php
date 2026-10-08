<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Sepatu Sneakers Urban', 'sku' => 'SKU-1001', 'description' => 'Sepatu sneakers kasual untuk pemakaian harian.'],
            ['name' => 'Tas Ransel Explorer', 'sku' => 'SKU-1002', 'description' => 'Tas ransel kapasitas 25L dengan slot laptop.'],
            ['name' => 'Jam Tangan Chrono Steel', 'sku' => 'SKU-1003', 'description' => 'Jam tangan analog dengan bodi stainless steel.'],
            ['name' => 'Headphone Wireless Pro', 'sku' => 'SKU-1004', 'description' => 'Headphone nirkabel dengan peredam bising aktif.'],
            ['name' => 'Jaket Parasut Windbreaker', 'sku' => 'SKU-1005', 'description' => 'Jaket tahan angin dengan lapisan dalam fleece.'],
        ];

        foreach ($products as $product) {
            Product::query()->firstOrCreate(['sku' => $product['sku']], $product + ['is_active' => true]);
        }
    }
}
