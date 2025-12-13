<?php

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;

// Check orders with missing customer data
echo "=== Checking Orders with Missing Customer Data ===\n\n";

$ordersWithMissingData = Order::where(function ($query) {
    $query->whereNull('customer_name')
        ->orWhereNull('customer_phone');
})->get();

echo "Found: " . $ordersWithMissingData->count() . " orders with missing customer data\n\n";

if ($ordersWithMissingData->count() > 0) {
    echo "Orders needing update:\n";
    echo str_repeat('-', 80) . "\n";

    foreach ($ordersWithMissingData as $order) {
        echo sprintf(
            "ID: %-4s | Invoice: %-15s | Name: %-20s | Phone: %-15s | User ID: %s\n",
            $order->id,
            $order->invoice_code,
            $order->customer_name ?? 'NULL',
            $order->customer_phone ?? 'NULL',
            $order->user_id
        );
    }

    echo "\n" . str_repeat('-', 80) . "\n\n";
    echo "Fix command will update these orders with data from their user relationship.\n";
} else {
    echo "✓ All orders have customer data populated!\n";
}

echo "\n=== Summary ===\n";
echo "Total Orders: " . Order::count() . "\n";
echo "Orders with Missing Data: " . $ordersWithMissingData->count() . "\n";
echo "Orders OK: " . (Order::count() - $ordersWithMissingData->count()) . "\n";
