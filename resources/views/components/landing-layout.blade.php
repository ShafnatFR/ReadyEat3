<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ReadyEat') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#F97316',
                        secondary: '#FBBF24',
                        dark: '#111827',
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white">

    {{-- Header --}}
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20l-5-5-5 5m0-16l5 5 5-5"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">ReadyEat</span>
                </a>

                <nav class="hidden md:flex md:space-x-8">
                    <a href="{{ url('/') }}"
                        class="text-gray-600 hover:text-orange-500 transition-colors {{ request()->is('/') ? 'text-orange-500 font-semibold' : '' }}">Home</a>
                    <a href="{{ url('/menus') }}"
                        class="text-gray-600 hover:text-orange-500 transition-colors {{ request()->is('menus') ? 'text-orange-500 font-semibold' : '' }}">Shop</a>
                    <a href="#" class="text-gray-600 hover:text-orange-500 transition-colors">About Us</a>
                </nav>

                <div>
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden sm:inline-block bg-amber-400 text-gray-900 font-semibold px-5 py-2 rounded-md hover:bg-amber-500 transition-colors">SIGN
                            IN</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20l-5-5-5 5m0-16l5 5 5-5"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-800">ReadyEat</span>
                    </a>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-500 hover:text-orange-500">About Us</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-orange-500">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Products</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/menus') }}" class="text-gray-500 hover:text-orange-500">Browse Menu</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-500 hover:text-orange-500">Help Center</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-orange-500">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-500 hover:text-orange-500"><svg class="w-6 h-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v7.028C18.343 21.128 22 16.991 22 12z" />
                            </svg></a>
                        <a href="#" class="text-gray-500 hover:text-orange-500"><svg class="w-6 h-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-8 pt-6 text-center">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} ReadyEat. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>