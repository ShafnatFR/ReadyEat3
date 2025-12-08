<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - ReadyEat</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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

    {{-- Theme Initialization Script - Must run before page render to prevent flash --}}
    <script>
            // Initialize theme from localStorage before page renders
            (function () {
                const savedTheme = localStorage.getItem('theme');
                const theme = savedTheme || 'light';

                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @media print {
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</head>

<body
    class="font-sans antialiased bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 transition-colors duration-200"
    x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark',
        mobileMenuOpen: false, 
        selectedOrderId: null, 
        editModalOpen: false, 
        editingProduct: null 
    }" x-init="
        $watch('darkMode', value => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            if (value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })
    ">

    {{-- Header --}}
    <header
        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 print:hidden transition-colors duration-200 relative">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Admin Panel</h1>
                <nav class="hidden md:flex items-center space-x-4">
                    <button @click="darkMode = !darkMode"
                        class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>
                    <a href="{{ url('/') }}"
                        class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium transition-colors">Exit
                        to Site</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium transition-colors">Logout</button>
                    </form>
                </nav>
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden text-gray-600 dark:text-gray-300 hover:text-primary focus:outline-none">
                    <svg x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="mobileMenuOpen" x-cloak
            class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 shadow-lg absolute w-full left-0 z-50">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="{{ url('/') }}"
                    class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">Exit
                    to Site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">Logout</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="container mx-auto p-4 sm:p-6 lg:p-8 print:p-0">
        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tabs Navigation --}}
        <div class="print:hidden mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    @foreach(['dashboard', 'verification', 'production', 'products', 'pickup', 'customers'] as $tabName)
                        <a href="{{ route('admin.dashboard', ['tab' => $tabName]) }}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm capitalize
                                                  {{ $tab === $tabName ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                            {{ ucfirst($tabName) }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        @if($tab === 'dashboard')
            @include('admin.tabs.dashboard', ['stats' => $stats])
        @elseif($tab === 'verification')
            @include('admin.tabs.verification', ['data' => $verificationData, 'products' => $products])
        @elseif($tab === 'production')
            @include('admin.tabs.production', ['productionRecap' => $productionRecap, 'productionDate' => $productionDate])
        @elseif($tab === 'products')
            @include('admin.tabs.products', ['products' => $products])
        @elseif($tab === 'pickup')
            @include('admin.tabs.pickup', ['pickupOrders' => $pickupOrders])
        @elseif($tab === 'customers')
            @include('admin.tabs.customers', ['customers' => $customers])
        @endif
    </main>

</body>

</html>