<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Random date within last 5 years
        $createdAt = Carbon::now()->subDays(rand(0, 1825)); // 5 years = 1825 days

        // Pickup date is 1-3 days after order
        $pickupDate = $createdAt->copy()->addDays(rand(1, 3));

        // Status distribution (realistic)
        $statusWeights = [
            'picked_up' => 60,      // 60% completed orders
            'cancelled' => 10,       // 10% cancelled
            'payment_pending' => 5,  // 5% pending payment
            'ready_for_pickup' => 10, // 10% ready
            'unpaid' => 15,          // 15% unpaid (abandoned carts)
        ];

        $status = $this->weightedRandom($statusWeights);

        // Customer info
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'user_id' => User::factory(), // Will be overridden by seeder
            'invoice_code' => 'INV-' . strtoupper(uniqid()),
            'status' => $status,
            'pickup_date' => $pickupDate->format('Y-m-d'),
            'notes' => rand(0, 10) > 7 ? fake()->sentence() : null, // 30% have notes
            'total_price' => 0, // Will be calculated after order items
            'customer_name' => $firstName . ' ' . $lastName,
            'customer_phone' => '08' . fake()->numerify('##########'),
            'created_at' => $createdAt,
            'updated_at' => $createdAt->copy()->addMinutes(rand(1, 60)),
        ];
    }

    /**
     * Helper function for weighted random selection
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);

        $sum = 0;
        foreach ($weights as $key => $weight) {
            $sum += $weight;
            if ($random <= $sum) {
                return $key;
            }
        }

        return array_key_first($weights);
    }

    /**
     * Order with specific date
     */
    public function onDate(Carbon $date): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date->copy()->addMinutes(rand(1, 60)),
            'pickup_date' => $date->copy()->addDays(rand(1, 3))->format('Y-m-d'),
        ]);
    }

    /**
     * Completed orders (picked up)
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'picked_up',
        ]);
    }

    /**
     * Cancelled orders
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}