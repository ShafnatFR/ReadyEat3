<x-landing-layout>
    <header class="bg-white/80 backdrop-blur-md sticky top-20 z-30 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-xl font-semibold text-gray-700">Product Listing</h1>
                <form action="{{ route('menus.index') }}" method="GET" class="flex items-center">
                    <select name="sort" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 cursor-pointer">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort by</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to
                            High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to
                            Low</option>
                        <option value="name_az" {{ request('sort') == 'name_az' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="relative rounded-lg overflow-hidden mb-8 h-64">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1200"
                class="w-full h-full object-cover" alt="Food Banner" />
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <h1 class="text-4xl font-extrabold text-white tracking-wider">Explore Our Delicious Selection</h1>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            {{-- Product Grid --}}
            <div class="w-full lg:flex-1">
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <div class="flex items-center space-x-2 rounded-full bg-gray-100 p-1">
                        <button
                            class="px-4 py-1.5 rounded-full bg-white shadow text-orange-500 font-semibold">All</button>
                        <button class="px-4 py-1.5 rounded-full text-gray-600 hover:bg-white/60">Popular</button>
                        <button class="px-4 py-1.5 rounded-full text-gray-600 hover:bg-white/60">Promo</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($menus as $menu)
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $menu->image ?? 'https://via.placeholder.com/400x300' }}"
                                    alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-800 line-clamp-1" title="{{ $menu->name }}">
                                    {{ $menu->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1 h-10 line-clamp-2">{{ $menu->description }}</p>

                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-lg font-bold text-orange-600">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </span>

                                    <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-orange-600 transition-colors flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span>Add</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cart Sidebar --}}
            <aside class="w-full lg:w-96 bg-gray-50 p-6 rounded-lg shadow-inner sticky top-24">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Selected Products
                </h2>

                @if(empty($cart))
                    <div class="text-center py-8">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">Your cart is empty.</p>
                    </div>
                @else
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                        @foreach($cart as $id => $details)
                            <div class="flex items-center space-x-4">
                                <img src="{{ $details['image'] ?? 'https://via.placeholder.com/60' }}"
                                    alt="{{ $details['name'] }}" class="w-16 h-16 rounded-md object-cover" />
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-700">{{ $details['name'] }}</p>
                                    <div class="flex items-center mt-1">
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="{{ $details['quantity'] - 1 }}">
                                            <button type="submit"
                                                class="text-gray-500 hover:text-gray-800 p-1 rounded-full bg-gray-200" {{ $details['quantity'] <= 1 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18 12H6" />
                                                </svg>
                                            </button>
                                        </form>
                                        <span class="px-3 font-semibold">{{ $details['quantity'] }}</span>
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="{{ $details['quantity'] + 1 }}">
                                            <button type="submit"
                                                class="text-gray-500 hover:text-gray-800 p-1 rounded-full bg-gray-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp
                                        {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-sm">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 border-t pt-6 space-y-3 text-gray-600">
                        <div class="flex justify-between"><span>Subtotal</span><span>Rp
                                {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>Rp
                                {{ number_format($shipping, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between font-bold text-lg text-gray-800"><span>Total</span><span>Rp
                                {{ number_format($total, 0, ',', '.') }}</span></div>
                        <a href="{{ route('checkout.index') }}"
                            class="block w-full bg-gray-900 text-white text-center py-3 rounded-lg mt-4 font-semibold hover:bg-gray-800 transition-colors text-lg">
                            Proceed to Payment
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </main>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
</x-landing-layout>