<?php

use Illuminate\Support\Facades\Schema;
use App\Models\OrderItem;
use App\Models\Order;

echo "=== DIAGNOSA HARGA ===\n";

// 1. Cek Kolom Database
echo "\n[1] Struktur Tabel 'order_items':\n";
$columns = Schema::getColumnListing('order_items');
echo implode(', ', $columns) . "\n";

// 2. Cek Order Terakhir yang Bermasalah (atau order item terbaru)
echo "\n[2] Sampel Data Item Terbaru:\n";
$item = OrderItem::with(['order', 'menu'])->latest()->first();

if ($item) {
    echo "Item ID: {$item->id}\n";
    echo "Order Invoice: " . ($item->order->invoice_code ?? 'N/A') . "\n";
    echo "Menu Name: " . ($item->menu->name ?? 'Deleted Menu') . "\n";
    echo "Quantity: {$item->quantity}\n";

    // Cek kemungkinan nama kolom harga
    echo "--- Nilai Harga di Database ---\n";
    echo "price_at_purchase: " . ($item->price_at_purchase ?? 'NULL') . "\n";
    echo "price: " . ($item->price ?? 'NULL') . "\n";
    echo "amount: " . ($item->amount ?? 'NULL') . "\n";
    echo "Menu Current Price (Master): " . ($item->menu->price ?? 'N/A') . "\n";
} else {
    echo "Belum ada data order item.\n";
}

// 3. Cek apakah ada data dengan harga 0
$zeroPriceCount = OrderItem::where('price_at_purchase', 0)
    ->orWhereNull('price_at_purchase')
    ->count();

echo "\n[3] Statistik Error:\n";
echo "Jumlah item dengan harga 0 atau NULL: $zeroPriceCount\n";
