<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'prof_image' => fake()->imageUrl(), // Simulasi path gambar bukti transfer
            'amount' => fake()->numberBetween(50000, 500000),
        ];
    }
}