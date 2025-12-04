<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dari constants.ts (React Project)
        $products = [
            [
                'name' => 'Gourmet Cheese Platter',
                'description' => 'A curated selection of artisanal cheeses, fruits, and nuts.',
                'price' => 24500,
                'image' => 'https://images.unsplash.com/photo-1626379245440-a6883a852a3a?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Organic Fruit Basket',
                'description' => 'A delightful assortment of fresh, seasonal organic fruits.',
                'price' => 18000,
                'image' => 'https://images.unsplash.com/photo-1593280432433-64ad6a4392a8?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Artisan Bread Assortment',
                'description' => 'Freshly baked artisan breads with a crispy crust.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1534352772702-7a58451c28a8?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deluxe Seafood Paella',
                'description' => 'Generous seafood paella featuring saffron-infused rice.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1626075983344-9f70a1a8c3d7?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vegan Power Bowl',
                'description' => 'A healthy and delicious bowl with quinoa and roasted vegetables.',
                'price' => 22000,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Spicy Kimchi Jar',
                'description' => 'Authentically fermented, perfect for a spicy kick to any meal.',
                'price' => 12500,
                'image' => 'https://images.unsplash.com/photo-1582450871972-ab69c4405391?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Craft Beer Selection',
                'description' => 'A curated selection of the finest local craft beers.',
                'price' => 23000,
                'image' => 'https://images.unsplash.com/photo-1584225010359-ac4183188599?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Homemade Pasta Kit',
                'description' => 'Everything you need for a gourmet pasta night at home.',
                'price' => 24000,
                'image' => 'https://images.unsplash.com/photo-1598866594240-a7df3a0c8b28?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Spicy Beef Ramen',
                'description' => 'Delicious spicy beef ramen perfect for cold days.', // Added dummy desc
                'price' => 23500,
                'image' => 'https://images.unsplash.com/photo-1612874421979-5ab803a31980?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Classic Margherita Pizza',
                'description' => 'Simple yet delicious classic pizza.', // Added dummy desc
                'price' => 22500,
                'image' => 'https://images.unsplash.com/photo-1598021680133-eb3a7fb159b3?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gourmet Cheeseburger',
                'description' => 'Juicy burger with premium cheese.', // Added dummy desc
                'price' => 21000,
                'image' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fresh Salmon Platter',
                'description' => 'Fresh salmon served with side dishes.', // Added dummy desc
                'price' => 24800,
                'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chicken Alfredo Pasta',
                'description' => 'Creamy alfredo pasta with grilled chicken.', // Added dummy desc
                'price' => 22000,
                'image' => 'https://images.unsplash.com/photo-1588013273468-4170b2b7aafa?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mango Sticky Rice',
                'description' => 'Sweet sticky rice with fresh mango.', // Added dummy desc
                'price' => 18500,
                'image' => 'https://images.unsplash.com/photo-1628840618239-a1130a0301e7?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Homemade Lemonade',
                'description' => 'Refreshing homemade lemonade.', // Added dummy desc
                'price' => 11000,
                'image' => 'https://images.unsplash.com/photo-1621263764928-df1444c53853?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grilled Fish',
                'description' => 'Freshly grilled fish with herbs and lemon.', // From heroProduct
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm, gooey chocolate cake with a molten center.',
                'price' => 19000,
                'image' => 'https://images.unsplash.com/photo-1542826438-643292d59490?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Cold-pressed juice made from fresh, ripe oranges.',
                'price' => 10500,
                'image' => 'https://images.unsplash.com/photo-1613482146955-c103a45c9a72?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Avocado Toast',
                'description' => 'Smashed avocado on toasted sourdough with chili flakes.',
                'price' => 17500,
                'image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sushi Platter',
                'description' => 'A beautiful assortment of fresh nigiri and maki rolls.',
                'price' => 24900,
                'image' => 'https://images.unsplash.com/photo-1592891398335-d25c7a421636?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DIY Taco Kit',
                'description' => 'Build your own tacos with seasoned meat, fresh toppings, and tortillas.',
                'price' => 23000,
                'image' => 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?q=80&w=800',
                'isAvaible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('menus')->insert($products);
    }
}