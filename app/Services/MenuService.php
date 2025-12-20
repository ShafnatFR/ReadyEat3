<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;

class MenuService
{
    /**
     * Search menus by keyword
     */
    public function search(string $query, ?string $category = null): Builder
    {
        return Menu::query()
            ->where('is_available', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get menus by category
     */
    public function getByCategory(string $category): Builder
    {
        return Menu::where('is_available', true)
            ->where('category', $category)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get available menus with caching
     */
    public function getAvailable(bool $useCache = true): \Illuminate\Support\Collection
    {
        if (!$useCache) {
            return Menu::where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return \Cache::remember('menus.available', 3600, function () {
            return Menu::where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get menu categories
     */
    public function getCategories(): array
    {
        return \Cache::remember('menu.categories', 3600, function () {
            return Menu::where('is_available', true)
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values()
                ->toArray();
        });
    }

    /**
     * Clear menu cache
     */
    public function clearCache(): void
    {
        \Cache::forget('menus.available');
        \Cache::forget('menu.categories');
    }
}
