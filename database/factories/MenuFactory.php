<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    // Memberitahu factory bahwa ini untuk model Menu
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(10000, 100000),
            'image' => fake()->imageUrl(640, 480, 'food'),
            'isAvaible' => true,
        ];
    }
}