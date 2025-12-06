<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CleanDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-dummy 
                            {--force : Force the operation without confirmation}
                            {--keep-recent=0 : Keep N most recent orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean dummy/seed data from orders table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepRecent = (int) $this->option('keep-recent');
        $force = $this->option('force');

        // Show current statistics
        $this->info('Current Database Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Orders', Order::count()],
                ['Completed Orders', Order::whereIn('status', ['picked_up', 'ready_for_pickup'])->count()],
                ['Pending Orders', Order::where('status', 'payment_pending')->count()],
                ['Cancelled Orders', Order::where('status', 'cancelled')->count()],
                ['Unique Customers', Order::distinct('customer_phone')->count('customer_phone')],
            ]
        );

        // Confirm before deletion
        if (!$force && !$this->confirm('Do you want to delete all order data?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Cleaning dummy data...');

        try {
            DB::beginTransaction();

            if ($keepRecent > 0) {
                // Get IDs of orders to keep
                $keepIds = Order::orderBy('created_at', 'desc')
                    ->limit($keepRecent)
                    ->pluck('id');

                // Delete order items for orders not in keep list
                $deletedItems = OrderItem::whereNotIn('order_id', $keepIds)->delete();

                // Delete orders not in keep list
                $deletedOrders = Order::whereNotIn('id', $keepIds)->delete();

                $this->info("Deleted {$deletedOrders} orders and {$deletedItems} order items.");
                $this->info("Kept {$keepRecent} most recent orders.");
            } else {
                // Delete all
                $deletedItems = OrderItem::count();
                OrderItem::truncate();

                $deletedOrders = Order::count();
                Order::truncate();

                $this->info("Deleted all {$deletedOrders} orders and {$deletedItems} order items.");
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ Database cleaned successfully!');

            // Show new statistics
            $this->newLine();
            $this->info('New Database Statistics:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Orders', Order::count()],
                    ['Order Items', OrderItem::count()],
                    ['Unique Customers', Order::distinct('customer_phone')->count('customer_phone')],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error cleaning database: ' . $e->getMessage());
            return 1;
        }
    }
}
