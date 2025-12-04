<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin/Test (Agar bisa login)
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@readyeat.com',
            'password' => Hash::make('password'), // Password admin
            'role' => 'admin', // Role kunci
        ]);

        // 2. Buat Akun Mahasiswa Dummy (Opsional, buat ngetes)
        User::create([
            'name' => 'Shafnat Mahasiswa',
            'email' => 'shafnat@student.telkom.ac.id',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 2. Buat 5 User Customer Acak
        $users = User::factory(5)->create();

        // 3. Buat 20 Menu Makanan
        $menus = Menu::factory(20)->create();

        // 4. Buat Transaksi untuk setiap User Customer
        foreach ($users as $user) {
            // Setiap user melakukan 1 sampai 3 pesanan
            $ordersCount = rand(1, 3);

            Order::factory($ordersCount)->create([
                'user_id' => $user->id,
                'total_price' => 0, // Nanti diupdate setelah item dimasukkan
            ])->each(function ($order) use ($menus) {

                // --- Mengisi Keranjang (Order Items) ---
                // Ambil 1 sampai 4 menu acak untuk pesanan ini
                $randomMenus = $menus->random(rand(1, 4));
                $totalOrderPrice = 0;

                foreach ($randomMenus as $menu) {
                    $quantity = rand(1, 3);
                    $price = $menu->price; // Harga sesuai menu saat itu

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => $quantity,
                        'price_at_purchase' => $price,
                    ]);

                    $totalOrderPrice += ($price * $quantity);
                }

                // --- Update Total Harga Order ---
                $order->update(['total_price' => $totalOrderPrice]);

                // --- Buat Pembayaran (Payment) ---
                Payment::factory()->create([
                    'order_id' => $order->id,
                    'amount' => $totalOrderPrice, // Bayar sesuai tagihan
                ]);
            });
        }
    }
}