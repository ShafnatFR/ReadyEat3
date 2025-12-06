<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random menu item
        $menu = Menu::inRandomOrder()->first();

        // Quantity between 1-5 (most orders have small quantities)
        $quantityWeights = [
            1 => 50,  // 50% order 1 item
            2 => 30,  // 30% order 2 items
            3 => 15,  // 15% order 3 items
            4 => 4,   // 4% order 4 items
            5 => 1,   // 1% order 5 items
        ];

        $quantity = $this->weightedRandom($quantityWeights);

        return [
            'order_id' => Order::factory(), // Will be overridden by seeder
            'menu_id' => $menu->id,
            'quantity' => $quantity,
            'price_at_purchase' => $menu->price, // Store price at time of purchase
        ];
    }

    /**
     * Helper function for weighted random selection
     */
    private function weightedRandom(array $weights): int
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
     * Set specific menu item
     */
    public function forMenu(int $menuId): static
    {
        $menu = Menu::find($menuId);

        return $this->state(fn(array $attributes) => [
            'menu_id' => $menuId,
            'price_at_purchase' => $menu->price,
        ]);
    }

    /**
     * Set specific quantity
     */
    public function quantity(int $qty): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => $qty,
        ]);
    }
}