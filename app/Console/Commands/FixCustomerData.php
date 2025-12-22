<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class FixCustomerData extends Command
{
    protected $signature = 'orders:fix-customer-data {--dry-run : Show what would be updated without actually updating}';
    protected $description = 'Fix orders with missing customer data by copying from user relationship';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('=== DRY RUN MODE - No changes will be made ===');
        } else {
            $this->info('=== Fixing Orders with Missing Customer Data ===');
        }
        $this->newLine();

        $ordersToFix = Order::where(function ($query) {
            $query->whereNull('customer_name')
                ->orWhereNull('customer_phone')
                ->orWhere('customer_name', '')
                ->orWhere('customer_phone', '');
        })->with('user')->get();

        if ($ordersToFix->count() === 0) {
            $this->success('✓ No orders need fixing. All customer data is populated!');
            return Command::SUCCESS;
        }

        $this->info('Found ' . $ordersToFix->count() . ' orders to fix');
        $this->newLine();

        $updated = 0;
        $failed = 0;

        foreach ($ordersToFix as $order) {
            if (!$order->user) {
                $this->error("Order {$order->invoice_code}: User not found (user_id: {$order->user_id})");
                $failed++;
                continue;
            }

            $newName = $order->customer_name ?: $order->user->name;
            $newPhone = $order->customer_phone ?: ($order->user->phone ?? '-');

            if ($dryRun) {
                $this->line("Would update Order {$order->invoice_code}: name='{$newName}', phone='{$newPhone}'");
            } else {
                try {
                    $order->update([
                        'customer_name' => $newName,
                        'customer_phone' => $newPhone,
                    ]);
                    $this->line("✓ Updated Order {$order->invoice_code}");
                    $updated++;
                } catch (\Exception $e) {
                    $this->error("✗ Failed to update Order {$order->invoice_code}: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->info('=== Dry Run Summary ===');
            $this->line('Orders that would be updated: ' . $ordersToFix->count());
            $this->newLine();
            $this->comment('Run without --dry-run to apply changes');
        } else {
            $this->info('=== Summary ===');
            $this->line('Successfully Updated: ' . $updated);
            if ($failed > 0) {
                $this->warn('Failed: ' . $failed);
            }
        }

        return Command::SUCCESS;
    }
}
