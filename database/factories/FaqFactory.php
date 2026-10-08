<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => rtrim($this->faker->unique()->sentence(8), '.').'?',
            'answer' => $this->faker->paragraph(3),
            'category' => $this->faker->randomElement(['Umum', 'Pengiriman', 'Refund', 'Produk']),
        ];
    }
}
