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
        // Get all products for best seller section
        $bestProducts = Menu::where('isAvailable', true)
            ->select('id', 'name', 'category', 'price', 'description', 'image')
            ->latest()
            ->limit(8)
            ->get();

        // Get featured products for carousel (you can customize these IDs)
        $featuredProductIds = [1, 2, 3, 4]; // Customize these IDs as needed
        $featuredProducts = Menu::whereIn('id', $featuredProductIds)
            ->where('isAvailable', true)
            ->select('id', 'name', 'category', 'price', 'description', 'image')
            ->get();

        // If no featured products or less than 4, use best products
        if ($featuredProducts->count() < 4) {
            $featuredProducts = $bestProducts->take(4);
        }

        return view('welcome', compact('bestProducts', 'featuredProducts'));
    }

    /**
     * Display product listing page with cart
     */
    public function index(Request $request)
    {
        // Get all available menus
        $query = Menu::where('isAvailable', true);

        // Apply category filter if requested
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

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

        $menus = $query->paginate(12);

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
