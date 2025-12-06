<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add item to cart
     */
    public function addToCart($id)
    {
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Menu ditambahkan ke keranjang!');
    }

    /**
     * Update cart quantity
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:menus,id',
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$validated['id']])) {
            $cart[$validated['id']]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', 'Item not found in cart.');
    }

    /**
     * Remove item from cart
     */
    public function removeCart(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$validated['id']])) {
            unset($cart[$validated['id']]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Item removed from cart.');
        }

        return redirect()->back()->with('error', 'Item not found in cart.');
    }
}
