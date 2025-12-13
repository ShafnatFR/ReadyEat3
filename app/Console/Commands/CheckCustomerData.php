<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class CheckCustomerData extends Command
{
    protected $signature = 'orders:check-customer-data';
    protected $description = 'Check orders for missing customer data (name/phone)';

    public function handle(): int
    {
        $this->info('=== Checking Orders for Missing Customer Data ===');
        $this->newLine();

        $ordersWithMissingData = Order::where(function ($query) {
            $query->whereNull('customer_name')
                ->orWhereNull('customer_phone')
                ->orWhere('customer_name', '')
                ->orWhere('customer_phone', '');
        })->with('user')->get();

        $this->info('Found: ' . $ordersWithMissingData->count() . ' orders with missing customer data');
        $this->newLine();

        if ($ordersWithMissingData->count() > 0) {
            $this->warn('Orders needing update:');

            $headers = ['ID', 'Invoice', 'Customer Name', 'Customer Phone', 'User ID', 'User Name', 'Status'];
            $rows = [];

            foreach ($ordersWithMissingData as $order) {
                $rows[] = [
                    $order->id,
                    $order->invoice_code,
                    $order->customer_name ?? 'NULL',
                    $order->customer_phone ?? 'NULL',
                    $order->user_id,
                    $order->user?->name ?? 'N/A',
                    $order->status,
                ];
            }

            $this->table($headers, $rows);
            $this->newLine();

            $this->info('To fix these orders, run: php artisan orders:fix-customer-data');
        } else {
            $this->success('✓ All orders have customer data populated!');
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->line('Total Orders: ' . Order::count());
        $this->line('Orders with Missing Data: ' . $ordersWithMissingData->count());
        $this->line('Orders OK: ' . (Order::count() - $ordersWithMissingData->count()));

        return Command::SUCCESS;
    }
}
