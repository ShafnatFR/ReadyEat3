@extends('layouts.app')

@section('title', 'Our Menu')

@section('content')
<header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                <span class="text-primary">Ready</span>Eat
            </a>
            <div class="text-xl font-semibold text-gray-700">Product Listing</div>
        </div>
    </div>
</header>

<main class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="relative rounded-lg overflow-hidden mb-8 h-64">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1200" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <h1 class="text-4xl font-extrabold text-white tracking-wider text-center">Explore Our Delicious Selection</h1>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 items-start">
        
        <div class="w-full lg:w-3/4">
            <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                <div class="flex items-center space-x-2 rounded-full bg-gray-100 p-1">
                    <button class="px-4 py-1.5 rounded-full bg-white shadow text-primary font-semibold">All</button>
                    <button class="px-4 py-1.5 rounded-full text-gray-600 hover:bg-white/60">Popular</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($menus as $menu)
                <div class="bg-white rounded-lg shadow-md overflow-hidden group transition-shadow hover:shadow-xl border border-gray-100">
                    <div class="relative">
                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $menu->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $menu->description }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-gray-800">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            
                            <form action="#" method="POST"> 
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-full font-semibold hover:bg-orange-600 transition-colors flex items-center space-x-2 text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    <span>Add</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <aside class="w-full lg:w-1/4 bg-gray-50 p-6 rounded-lg shadow-inner sticky top-24 border border-gray-200">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Cart
            </h2>

            @if(empty($cart))
                <p class="text-gray-500 text-center py-4">Keranjang Anda kosong.</p>
            @else
                <div class="space-y-4">
                    @foreach($cart as $item)
                    <div class="flex items-center space-x-4">
                        <div class="flex-grow">
                            <p class="font-semibold text-gray-700 text-sm">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($item['price'],0,',','.') }} x {{ $item['quantity'] }}</p>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm">
                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 border-t pt-6 space-y-3 text-gray-600 text-sm">
                    <div class="flex justify-between font-bold text-lg text-gray-800">
                        <span>Total</span>
                        <span>Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <button class="w-full bg-dark text-white py-3 rounded-lg mt-4 font-semibold hover:bg-gray-800 transition-colors">
                        Proceed to Payment
                    </button>
                </div>
            @endif
        </aside>

    </div>
</main>
@endsection