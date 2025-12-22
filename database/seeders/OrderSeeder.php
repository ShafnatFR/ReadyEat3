<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Customer ID 2 (Shafnat Mahasiswa)
        $customerId = 2;

        // Create 15 dummy orders with varying dates
        for ($i = 1; $i <= 15; $i++) {
            // Random date within last 30 days
            $daysAgo = rand(0, 30);
            $orderDate = Carbon::now()->subDays($daysAgo);

            // Random status (matching database enum)
            $statuses = ['unpaid', 'payment_pending', 'ready_for_pickup', 'picked_up', 'cancelled'];
            $status = $statuses[array_rand($statuses)];

            // Create order
            $order = Order::create([
                'user_id' => $customerId,
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => $status,
                'pickup_date' => $orderDate->copy()->addDays(1)->format('Y-m-d'),
                'notes' => 'Dummy order #' . $i,
                'total_price' => 0, // Will calculate later
                'customer_name' => 'Shafnat Mahasiswa',
                'customer_phone' => '081234567890',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add 2-4 random items to each order
            $itemCount = rand(2, 4);
            $totalPrice = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $menuId = rand(1, 21); // We have 21 menus
                $quantity = rand(1, 3);
                $price = rand(10000, 25000); // Approximate price range

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'price_at_purchase' => $price,
                ]);

                $totalPrice += ($price * $quantity);
            }

            // Add shipping cost
            $totalPrice += 15000;

            // Update order total
            $order->update(['total_price' => $totalPrice]);

            // Create payment record (using correct column names)
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'proof_image' => 'payments/dummy_proof_' . $i . '.jpg',
            ]);
        }
    }
}
