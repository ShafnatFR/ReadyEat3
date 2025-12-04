<x-landing-layout>
    <header class="bg-white/80 backdrop-blur-md sticky top-16 z-30 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-xl font-semibold text-gray-700">Daftar Menu</h1>
                
                <form action="{{ route('menus.index') }}" method="GET" class="flex items-center">
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 cursor-pointer">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="name_az" {{ request('sort') == 'name_az' ? 'selected' : '' }}>Nama: A-Z</option>
                    </select>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <div class="relative rounded-xl overflow-hidden mb-8 h-48 md:h-64 shadow-lg">
           <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1200" class="w-full h-full object-cover" alt="Food Banner">
           <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-wider text-center px-4">
                    Jelajahi Rasa Terbaik Kami
                </h2>
           </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                   <a href="{{ route('menus.index') }}" class="px-4 py-1.5 rounded-full {{ !request('category') ? 'bg-orange-500 text-white shadow' : 'bg-white text-gray-600 hover:bg-gray-100' }} text-sm font-semibold transition">
                       Semua
                   </a>
                   <button class="px-4 py-1.5 rounded-full bg-white text-gray-600 hover:bg-gray-100 text-sm transition">Makanan Berat</button>
                   <button class="px-4 py-1.5 rounded-full bg-white text-gray-600 hover:bg-gray-100 text-sm transition">Minuman</button>
                   <button class="px-4 py-1.5 rounded-full bg-white text-gray-600 hover:bg-gray-100 text-sm transition">Snack</button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($menus as $menu)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $menu->image ?? 'https://via.placeholder.com/400x300' }}" 
                                 alt="{{ $menu->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800 line-clamp-1" title="{{ $menu->name }}">{{ $menu->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1 h-10 line-clamp-2">{{ $menu->description }}</p>
                            
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-lg font-bold text-orange-600">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                                
                                <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-orange-600 transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <aside class="w-full lg:w-96 bg-white border border-gray-200 p-6 rounded-xl shadow-sm sticky top-24">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center border-b pb-4">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Keranjang Anda
                </h2>

                @if(empty($cart))
                    <div class="text-center py-8">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-gray-500">Keranjang masih kosong.</p>
                        <p class="text-xs text-gray-400 mt-1">Yuk pilih menu favoritmu!</p>
                    </div>
                @else
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($cart as $id => $details)
                        <div class="flex items-start gap-3">
                            <img src="{{ $details['image'] ?? 'https://via.placeholder.com/60' }}" alt="{{ $details['name'] }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100 bg-gray-50">
                            
                            <div class="flex-grow">
                                <h4 class="font-semibold text-gray-700 text-sm line-clamp-1">{{ $details['name'] }}</h4>
                                <p class="text-xs text-gray-500 mb-2">@ Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center border border-gray-200 rounded-lg">
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="{{ $details['quantity'] - 1 }}">
                                            <button type="submit" class="px-2 py-1 text-gray-500 hover:text-orange-600 disabled:opacity-50" {{ $details['quantity'] <= 1 ? 'disabled' : '' }}>-</button>
                                        </form>
                                        
                                        <span class="px-2 text-sm font-medium text-gray-800 w-6 text-center">{{ $details['quantity'] }}</span>
                                        
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="quantity" value="{{ $details['quantity'] + 1 }}">
                                            <button type="submit" class="px-2 py-1 text-gray-500 hover:text-orange-600">+</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Biaya Layanan</span>
                            <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-100 pt-2 mt-2">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        
                        <a href="{{ route('checkout.index') }}" class="block w-full bg-orange-600 text-white text-center py-3 rounded-xl mt-4 font-semibold hover:bg-orange-700 transition-colors shadow-lg shadow-orange-200">
                            Lanjut Pembayaran
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </main>
    
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
</x-landing-layout>