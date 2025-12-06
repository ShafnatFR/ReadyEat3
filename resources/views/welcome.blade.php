<x-landing-layout>
    {{-- Hero Section with Carousel --}}
    <section class="relative w-full py-12 md:py-20 bg-orange-50 overflow-hidden flex items-center min-h-[650px]" x-data="{
        currentIndex: 0,
        isAnimating: false,
        carouselProducts: {{ json_encode($featuredProducts ?? $bestProducts->take(4)) }},
        autoplay: null,
        init() {
            this.startAutoplay();
        },
        startAutoplay() {
            this.autoplay = setInterval(() => {
                this.next();
            }, 5000);
        },
        stopAutoplay() {
            if (this.autoplay) {
                clearInterval(this.autoplay);
            }
        },
        next() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            setTimeout(() => {
                this.currentIndex = (this.currentIndex + 1) % this.carouselProducts.length;
                this.isAnimating = false;
            }, 500);
        },
        prev() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            setTimeout(() => {
                this.currentIndex = (this.currentIndex - 1 + this.carouselProducts.length) % this.carouselProducts.length;
                this.isAnimating = false;
            }, 500);
        },
        goTo(index) {
            if (index === this.currentIndex || this.isAnimating) return;
            this.isAnimating = true;
            setTimeout(() => {
                this.currentIndex = index;
                this.isAnimating = false;
            }, 500);
        }
    }">
        <div
            class="absolute top-0 right-0 w-2/3 h-full bg-gradient-to-l from-yellow-100/50 to-transparent rounded-l-full blur-3xl -z-10 opacity-60">
        </div>
        <div class="absolute bottom-0 left-0 w-1/3 h-1/2 bg-orange-200/30 rounded-full blur-3xl -z-10"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Text Content --}}
                <div class="flex flex-col items-center lg:items-start text-center lg:text-left space-y-6">
                    <template x-for="(product, index) in carouselProducts" :key="product.id">
                        <div x-show="currentIndex === index"
                            :class="{ 'opacity-0 translate-y-4': isAnimating, 'opacity-100 translate-y-0': !isAnimating }"
                            class="transition-all duration-500 transform">
                            <span
                                class="inline-block py-1 px-4 rounded-full bg-orange-100 text-primary text-sm font-bold tracking-wider mb-4 border border-orange-200 shadow-sm"
                                x-text="product.category"></span>
                            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-4">
                                The Best <span class="text-primary" x-text="product.name"></span>
                                <br /> in Town
                            </h1>
                            <p class="text-gray-600 text-base sm:text-lg max-w-lg mx-auto lg:mx-0 mb-8 leading-relaxed"
                                x-text="product.description || 'Rasakan kenikmatan cita rasa otentik yang disiapkan dengan bahan-bahan pilihan terbaik. Pesan sekarang dan nikmati di rumah.'">
                            </p>
                            <div class="flex flex-col sm:flex-row items-center gap-6 justify-center lg:justify-start">
                                <div class="text-4xl font-black text-gray-800 tracking-tight">
                                    Rp <span x-text="product.price?.toLocaleString('id-ID')"></span>
                                </div>
                                <a href="{{ route('menus.index') }}"
                                    class="bg-gray-900 text-white px-8 py-4 rounded-full text-lg font-bold uppercase tracking-wider hover:bg-primary transition-all shadow-xl hover:shadow-2xl hover:scale-105 flex items-center gap-2">
                                    <span>Order Now</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </template>

                    {{-- Carousel Dots --}}
                    <div class="flex space-x-3 mt-8 pt-4">
                        <template x-for="(product, idx) in carouselProducts" :key="idx">
                            <button @click="goTo(idx)"
                                :class="idx === currentIndex ? 'bg-gray-900 w-8' : 'bg-gray-300 w-2 hover:bg-gray-400'"
                                class="h-2 rounded-full transition-all duration-300"
                                :aria-label="'Go to slide ' + (idx + 1)">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Image Carousel --}}
                <div class="relative h-[400px] sm:h-[500px] w-full flex items-center justify-center lg:justify-end">
                    <div class="relative w-full max-w-md lg:max-w-full h-full">
                        <div
                            class="absolute inset-0 rounded-[40px] md:rounded-[60px] overflow-hidden shadow-2xl transform transition-transform duration-500 hover:scale-[1.02] border-4 border-white/50">
                            <template x-for="(product, index) in carouselProducts" :key="product.id">
                                <div x-show="currentIndex === index"
                                    :class="currentIndex === index ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-110 z-0'"
                                    class="absolute inset-0 w-full h-full transition-all duration-700 ease-in-out">
                                    <img :src="product.image" :alt="product.name"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-60">
                                    </div>
                                </div>
                            </template>

                            {{-- Navigation Buttons --}}
                            <div class="absolute bottom-6 right-6 z-20 flex gap-3">
                                <button @click="prev()"
                                    class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white hover:text-gray-900 transition-colors border border-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </button>
                                <button @click="next()"
                                    class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white hover:text-gray-900 transition-colors border border-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Decorative Elements --}}
                        <div
                            class="absolute -bottom-6 -right-6 w-full h-full rounded-[60px] border-2 border-primary/20 -z-10 hidden lg:block">
                        </div>
                        <div
                            class="absolute -top-6 -left-6 w-24 h-24 bg-yellow-400 rounded-full blur-2xl opacity-40 -z-10">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories Section --}}
    <section class="bg-white py-12 transition-colors">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl sm:text-4xl font-bold text-gray-800">Our Categories</h2>
                <p class="text-gray-500 mt-1 text-sm sm:text-base">Find what you're looking for.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-2 sm:gap-4">
                @php
                    $categories = [
                        ['name' => 'Meals', 'image' => 'https://images.unsplash.com/photo-1543339308-43e59d6b70a6?q=80&w=800', 'category' => 'Meal'],
                        ['name' => 'Snacks', 'image' => 'https://images.unsplash.com/photo-1519915028121-7d3463d9e052?q=80&w=800', 'category' => 'Snack'],
                        ['name' => 'Drinks', 'image' => 'https://images.unsplash.com/photo-1551024709-8f237c20454d?q=80&w=800', 'category' => 'Drink'],
                        ['name' => 'Dessert', 'image' => 'https://images.unsplash.com/photo-1628840618239-a1130a0301e7?q=80&w=800', 'category' => 'Dessert'],
                        ['name' => 'Kit', 'image' => 'https://images.unsplash.com/photo-1598866594240-a7df3a0c8b28?q=80&w=800', 'category' => 'Kit'],
                    ];
                @endphp
                @foreach ($categories as $cat)
                    <a href="{{ route('menus.index', ['category' => $cat['category']]) }}"
                        class="relative group overflow-hidden rounded-xl aspect-square focus:outline-none focus:ring-2 focus:ring-primary w-[31%] sm:w-[22%] md:w-[18%]">
                        <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 brightness-90 group-hover:brightness-75">
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition-colors">
                            <h3
                                class="text-white text-[10px] sm:text-lg font-bold tracking-wider drop-shadow-md bg-black/30 px-2 py-1 rounded-full backdrop-blur-sm">
                                {{ $cat['name'] }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Best Seller Menu Section --}}
    <section class="py-12 sm:py-24 bg-gray-50 transition-colors">
        <div class="container mx-auto px-2 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-16">
                <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">Best Seller Menu</h2>
                <p class="text-sm sm:text-lg text-gray-600">Menu favorit pelanggan kami minggu ini.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-8">
                @foreach ($bestProducts as $product)
                    <div
                        class="group bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden flex flex-col h-full border border-gray-100 cursor-pointer">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            <div
                                class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors duration-300">
                            </div>
                            <div class="absolute top-1 left-1 sm:top-3 sm:left-3">
                                <span
                                    class="bg-white/90 backdrop-blur-sm text-gray-800 text-[8px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-3 sm:py-1.5 rounded-full shadow-sm">{{ $product->category }}</span>
                            </div>
                        </div>
                        <div class="p-2 sm:p-5 flex flex-col flex-grow relative">
                            <h3
                                class="text-[10px] sm:text-lg font-bold text-gray-900 mb-1 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                                {{ $product->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-500 mb-2 sm:mb-4 line-clamp-2 hidden sm:block">
                                {{ $product->description ?? 'Nikmati hidangan lezat ini bersama keluarga.' }}</p>
                            <div class="mt-auto flex items-end justify-between gap-1 sm:gap-2">
                                <div class="flex flex-col">
                                    <p class="hidden sm:block text-[10px] sm:text-xs text-gray-400 font-medium">Harga</p>
                                    <p class="text-[10px] sm:text-xl font-extrabold text-primary leading-tight">Rp
                                        {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="bg-gray-100 text-gray-900 p-1 sm:p-2.5 rounded-full group-hover:bg-primary group-hover:text-white transition-colors shadow-sm flex-shrink-0">
                                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 sm:mt-16 text-center">
                <a href="{{ route('menus.index') }}"
                    class="inline-flex items-center justify-center bg-gray-900 text-white px-6 py-3 sm:px-10 sm:py-4 rounded-full text-sm sm:text-lg font-bold hover:bg-primary hover:text-white transition-all shadow-lg hover:shadow-primary/30 transform hover:scale-105">Lihat
                    Semua Menu</a>
            </div>
        </div>
    </section>
</x-landing-layout>