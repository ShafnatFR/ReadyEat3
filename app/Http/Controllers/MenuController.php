<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // Mengambil semua menu untuk halaman listing
        $menus = \App\Models\Menu::all();

        // Dummy Cart Data (Karena kita belum buat logika session cart)
        // Nanti ini diganti dengan session('cart')
        $cart = [];
        $total = 0;

        return view('menus.index', compact('menus', 'cart', 'total'));
    }

    public function home()
    {
        // Mengambil 4 menu termahal/terbaik untuk ditampilkan di Landing Page
        $bestProducts = \App\Models\Menu::limit(4)->get();

        return view('home', compact('bestProducts'));
    }
}
