<x-landing-layout>
    {{-- Hero Section --}}
    <section class="bg-orange-500 pt-8 pb-20 text-white rounded-br-[80px] overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div class="space-y-6 md:text-left text-center">
                    <h1 class="text-5xl md:text-6xl font-light leading-tight">
                        Delicious Every <br /> <span class="font-extrabold">FOOD</span> bite
                    </h1>
                    <p class="text-orange-100 text-lg max-w-md mx-auto md:mx-0">
                        A well-abstracted restaurant description should include a clear and concise overview of your
                        establishment.
                    </p>
                    <div class="flex items-baseline space-x-4 justify-center md:justify-start">
                        <span class="text-5xl font-bold">Rp 25.000</span>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('menus.index') }}"
                            class="inline-block bg-amber-400 text-gray-900 px-8 py-3 rounded-md text-lg font-bold hover:bg-amber-300 transition-transform hover:scale-105 shadow-lg">
                            ORDER NOW
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex justify-center items-center">
                    <img src="https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=800"
                        alt="Delicious Food"
                        class="w-full max-w-md rounded-full object-cover shadow-2xl border-4 border-white/20" />
                </div>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="bg-white py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                @php
                    $categories = [
                        ['name' => 'Daily Catering', 'image' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?q=80&w=800'],
                        ['name' => 'Ready Meals', 'image' => 'https://images.unsplash.com/photo-1543339308-43e59d6b70a6?q=80&w=800'],
                        ['name' => 'Take Away', 'image' => 'https://images.unsplash.com/photo-1588611914238-601445778a48?q=80&w=800'],
                    ];
                @endphp
                @foreach($categories as $index => $cat)
                    <div
                        class="bg-white rounded-lg overflow-hidden shadow-lg cursor-pointer group p-4 text-center border-2 {{ $index === 0 ? 'border-orange-500' : 'border-transparent' }}">
                        <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}"
                            class="w-full h-56 object-cover rounded-md group-hover:scale-105 transition-transform duration-300" />
                        <h3 class="text-gray-800 text-xl font-bold mt-4">{{ $cat['name'] }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Best Products --}}
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-12 text-gray-800">Best Product</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($bestProducts as $product)
                    <div
                        class="bg-white rounded-lg shadow-md p-6 group text-center hover:shadow-xl transition-shadow flex flex-col items-center">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                            class="w-40 h-40 object-cover mb-4 transform group-hover:scale-105 transition-transform rounded-full" />
                        <h3 class="text-xl font-semibold text-gray-800 flex-grow">{{ $product->name }}</h3>
                        <p class="text-lg font-bold text-orange-500 mt-2">Rp
                            {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-12">
                <a href="{{ route('menus.index') }}"
                    class="inline-block bg-amber-400 text-gray-900 px-8 py-3 rounded-md text-lg font-bold hover:bg-amber-500 transition-colors shadow-md">
                    Load More
                </a>
            </div>
        </div>
    </section>
</x-landing-layout>