<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar: Ambil hanya menu yang tersedia
        $query = Menu::where('isAvaible', true);

        // 2. Fitur Filter Kategori (Optional, jika nanti kolom category ditambahkan)
        // Di Seeder tadi belum ada kolom category, tapi kita siapkan logikanya.
        // Jika kamu menambahkan kolom category di migration nanti, uncomment baris ini:
        // if ($request->has('category') && $request->category != 'All') {
        //    $query->where('category', $request->category);
        // }

        // 3. Fitur Sorting (Sesuai dropdown di desain)
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_az':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest(); // Default urutan terbaru
        }

        $menus = $query->get();

        // 4. Data Keranjang (Cart) dari Session
        $cart = session()->get('cart', []);

        // 5. Hitung Total Keranjang
        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Flat shipping rate Rp 15.000 jika ada barang, else 0
        $shipping = $subtotal > 0 ? 15000 : 0;
        $total = $subtotal + $shipping;

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'shipping', 'total'));
    }
}