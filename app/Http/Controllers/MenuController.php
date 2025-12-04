<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display landing page
     */
    public function home()
    {
        // Get 8 best products for landing page
        $bestProducts = Menu::where('isAvailable', true)
            ->latest()
            ->limit(8)
            ->get();

        return view('welcome', compact('bestProducts'));
    }

    /**
     * Display product listing page with cart
     */
    public function index(Request $request)
    {
        // Get all available menus
        $query = Menu::where('isAvailable', true);

        // Apply sorting if requested
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
            $query->latest();
        }

        $menus = $query->get();

        // Get cart from session
        $cart = session()->get('cart', []);

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = $subtotal > 0 ? 15000 : 0;
        $total = $subtotal + $shipping;

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'shipping', 'total'));
    }
}
