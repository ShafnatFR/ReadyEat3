<?php
use App\Models\OrderItem;

echo "=== VERIFIKASI ACCESSOR ===\n";
$item = OrderItem::find(18999);
if ($item) {
    echo "Item ID: " . $item->id . "\n";
    echo "Price (via Accessor \$item->price): " . $item->price . "\n";
    echo "Price (via DB Column \$item->price_at_purchase): " . $item->price_at_purchase . "\n";
} else {
    echo "Item 18999 not found, grabbing latest...\n";
    $item = OrderItem::latest()->first();
    echo "Item ID: " . $item->id . "\n";
    echo "Price (via Accessor \$item->price): " . $item->price . "\n";
}
