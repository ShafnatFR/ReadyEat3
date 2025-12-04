<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // 1. Ambil semua menu dari database
        $menus = Menu::all();

        // 2. Ambil data keranjang dari Session (Default array kosong jika belum ada)
        $cart = session()->get('cart', []);

        // 3. Hitung Subtotal, Shipping, dan Total (Logic dari React dipindah ke sini)
        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Logic Shipping: Jika ada belanjaan, ongkir 15.000, jika tidak 0
        $shipping = $subtotal > 0 ? 15000 : 0;
        $total = $subtotal + $shipping;

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'shipping', 'total'));
    }

    public function home()
    {
        $bestProducts = Menu::limit(4)->get();
        return view('home', compact('bestProducts'));
    }
}