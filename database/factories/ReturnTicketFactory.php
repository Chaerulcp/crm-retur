<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnTicket>
 */
class ReturnTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_number' => sprintf('RET-%s-%04d', now()->format('Ymd'), $this->faker->unique()->numberBetween(1, 9999)),
            'customer_id' => Customer::factory(),
            'product_id' => Product::factory(),
            'invoice_number' => strtoupper($this->faker->bothify('INV-#####')),
            'reason' => $this->faker->sentence(15),
            'status' => TicketStatus::Diajukan,
            'item_condition' => \App\Enums\ItemCondition::BelumDiterima,
            'tracking_token' => Str::random(40),
            'chat_active' => false,
        ];
    }

    public function status(TicketStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
