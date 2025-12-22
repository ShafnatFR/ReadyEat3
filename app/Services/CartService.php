<?php

namespace App\Services;

use App\Models\Menu;

class CartService
{
    /**
     * Get filtered and validated cart items.
     */
    public function getValidatedCart(): array
    {
        $cart = session()->get('cart');

        if (!$cart || !is_array($cart)) {
            return [];
        }

        $validCart = [];
        $hasInvalidItems = false;
        $updates = false;

        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);

            // Check existence
            if (!$menu) {
                $hasInvalidItems = true;
                session()->forget("cart.{$id}");
                continue;
            }

            // Check availability
            if (!$menu->is_available) {
                $hasInvalidItems = true;
                session()->forget("cart.{$id}");
                continue;
            }

            // Check price changes
            if ($menu->price != $details['price']) {
                $details['price'] = $menu->price;
                $cart[$id] = $details;
                $updates = true;
            }

            $validCart[$id] = $details;
        }

        // Save updates if needed
        if ($updates || $hasInvalidItems) {
            session()->put('cart', $validCart);
        }

        // Store flash message if items were removed
        if ($hasInvalidItems) {
            session()->flash('warning', 'Beberapa item di keranjang tidak tersedia dan telah dihapus.');
        }

        return $validCart;
    }

    /**
     * Calculate totals for the cart.
     */
    public function calculateTotals(array $cart): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = config('orders.shipping_fee', 15000);
        $total = $subtotal + $shipping;

        return compact('subtotal', 'shipping', 'total');
    }

    /**
     * Clear the cart.
     */
    public function clear(): void
    {
        session()->forget('cart');
    }

    /**
     * Check simple availability without locking (for display).
     */
    public function validateMenuAvailability(array $cart): array
    {
        $errors = [];
        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);
            if (!$menu) {
                $errors['cart'] = "Menu dengan ID {$id} tidak ditemukan.";
                continue;
            }
            if (!$menu->isAvailable) {
                $errors['cart'] = "Menu '{$menu->name}' saat ini tidak tersedia.";
            }
        }
        return $errors;
    }
}
