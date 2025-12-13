<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting OPTIMIZED database seeding (for better performance)...');
        $this->command->newLine();

        // ===== STEP 0: Clean Old Data (Prevent Duplicates/Bloat) =====
        $this->command->info('🧹 Cleaning old transaction data...');
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Payment::truncate();
        OrderItem::truncate();
        Order::truncate();
        // User::truncate(); // Aktifkan jika ingin reset user juga
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        $this->command->info('✅ Old transaction data cleared');
        $this->command->newLine();

        // ===== STEP 1: Create Admin Accounts =====
        $this->command->info('👤 Creating admin accounts...');

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'shafnat@readyeat.com'],
            [
                'name' => 'Shafnat',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Created 2 admin accounts');
        $this->command->newLine();

        // ===== STEP 2: Seed Menu Products =====
        $this->command->info('🍔 Seeding menu products...');
        $this->call(MenuSeeder::class);
        $this->command->info('✅ Created 21 menu items');
        $this->command->newLine();

        // ===== STEP 3: Create 200 Customers (OPTIMIZED) =====
        $this->command->info('👥 Creating 200 customers (5-year span) - OPTIMIZED...');
        $this->seedCustomers();
        $this->command->info('✅ Created 200 customer accounts');
        $this->command->newLine();

        // ===== STEP 4: Create ~10,000 Orders (OPTIMIZED) =====
        $this->command->info('📦 Creating ~10,000 orders (5-year span) - OPTIMIZED...');
        $this->seedOrders();
        $this->command->newLine();

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        $this->showStatistics();
    }

    /**
     * Seed 200 customers - OPTIMIZED
     */
    private function seedCustomers(): void
    {
        $totalCustomers = 200; // Reduced from 700

        $bar = $this->command->getOutput()->createProgressBar(4); // 4 chunks of 50
        $bar->start();

        for ($i = 0; $i < 4; $i++) {
            $customers = [];
            for ($j = 0; $j < 50; $j++) {
                $daysAgo = rand(0, 1825); // 5 years
                $createdAt = Carbon::now()->subDays($daysAgo);

                $firstName = fake()->firstName();
                $lastName = fake()->lastName();

                $customers[] = [
                    'name' => $firstName . ' ' . $lastName,
                    'email' => strtolower($firstName . '.' . $lastName . rand(1, 9999)) . '@' . fake()->freeEmailDomain(),
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            User::insert($customers);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
    }

    /**
     * Seed ~10,000 orders - OPTIMIZED for performance
     */
    private function seedOrders(): void
    {
        $customerIds = User::where('role', 'customer')->pluck('id')->toArray();
        $menus = Menu::all(); // Cache menus

        $startDate = Carbon::now()->subYears(5)->startOfMonth();
        $totalOrders = 0;

        $bar = $this->command->getOutput()->createProgressBar(60); // 60 months
        $bar->start();

        for ($month = 0; $month < 60; $month++) {
            $currentMonth = $startDate->copy()->addMonths($month);

            // OPTIMIZED: 100-250 orders/month (instead of 500-1200)
            $baseOrders = 100 + ($month * 2.5);
            $seasonalMultiplier = $this->getSeasonalMultiplier($currentMonth->month);
            $ordersThisMonth = (int) ($baseOrders * $seasonalMultiplier);

            for ($i = 0; $i < $ordersThisMonth; $i++) {
                $orderDate = $currentMonth->copy()
                    ->addDays(rand(0, $currentMonth->daysInMonth - 1))
                    ->addHours(rand(8, 20))
                    ->addMinutes(rand(0, 59));

                $userId = $customerIds[array_rand($customerIds)];
                $user = User::find($userId);

                // Create order
                $order = Order::create([
                    'user_id' => $userId,
                    'invoice_code' => 'INV-' . strtoupper(uniqid()),
                    'status' => $this->getRandomStatus(),
                    'pickup_date' => $orderDate->copy()->addDays(rand(1, 3))->format('Y-m-d'),
                    'notes' => rand(0, 10) > 8 ? fake()->sentence() : null,
                    'total_price' => 0,
                    'customer_name' => $user->name,
                    'customer_phone' => '08' . fake()->numerify('##########'),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Add 1-2 items per order (REDUCED from 1-4)
                $itemCount = rand(1, 2);
                $orderTotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $menu = $menus->random();
                    $quantity = rand(1, 2); // Max 2

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => $quantity,
                        'price_at_purchase' => $menu->price,
                    ]);

                    $orderTotal += ($menu->price * $quantity);
                }

                $orderTotal += 15000; // Shipping
                $order->update(['total_price' => $orderTotal]);

                // Create payment
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $orderTotal,
                    'proof_image' => 'payments/proof_' . $order->id . '.jpg',
                ]);

                $totalOrders++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("✅ Created $totalOrders orders with items and payments");
    }

    /**
     * Get seasonal multiplier
     */
    private function getSeasonalMultiplier(int $month): float
    {
        return match ($month) {
            12, 1, 2 => 1.3,    // Holiday peak
            6, 7, 8 => 1.2,      // Summer
            11 => 1.25,          // Pre-holiday
            3, 4, 5 => 0.9,      // Spring slow
            default => 1.0,
        };
    }

    /**
     * Get random status
     */
    private function getRandomStatus(): string
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 60 => 'picked_up',
            $rand <= 70 => 'cancelled',
            $rand <= 78 => 'ready_for_pickup',
            $rand <= 83 => 'payment_pending',
            default => 'unpaid',
        };
    }

    /**
     * Show statistics
     */
    private function showStatistics(): void
    {
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['picked_up', 'ready_for_pickup'])->sum('total_price');
        $totalMenus = Menu::count();

        $this->command->info('📊 Database Statistics (OPTIMIZED):');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Users', number_format($totalUsers)],
                ['└─ Customers', number_format($totalCustomers)],
                ['└─ Admins', $totalAdmins],
                ['Total Orders', number_format($totalOrders)],
                ['Total Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.')],
                ['Menu Items', $totalMenus],
                ['Date Range', '2020-01-01 to ' . date('Y-m-d')],
                ['Performance', '✅ Optimized for low-spec hardware'],
            ]
        );
    }
}