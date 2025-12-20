<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Display landing page
     */
    public function home()
    {
        // Get all products for best seller section
        $bestProducts = Menu::where('is_available', true)
            ->select('id', 'name', 'category', 'price', 'description', 'image')
            ->latest()
            ->limit(8)
            ->get();

        // Get featured products for carousel
        $featuredProductIds = [1, 2, 3, 4];
        $featuredProducts = Menu::whereIn('id', $featuredProductIds)
            ->where('is_available', true)
            ->select('id', 'name', 'category', 'price', 'description', 'image')
            ->get();

        if ($featuredProducts->count() < 4) {
            $featuredProducts = $bestProducts->take(4);
        }

        return view('welcome', compact('bestProducts', 'featuredProducts'));
    }

    /**
     * Display product listing page with search and filters - P3 Enhancement
     */
    public function index(Request $request)
    {
        $query = Menu::where('is_available', true);

        // P3: Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Sorting
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

        // Get categories for filter dropdown
        $categories = $this->menuService->getCategories();

        // Get cart from session
        $cart = session()->get('cart', []);

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = $subtotal > 0 ? 15000 : 0;
        $total = $subtotal + $shipping;

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'shipping', 'total', 'categories'));
    }
}
