<x-landing-layout>
    <main class="container mx-auto p-4 sm:p-6 lg:p-8 min-h-[60vh] bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">My Account</h1>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-8 items-start" x-data="{ activeTab: '{{ $activeTab }}' }">
                {{-- Sidebar Navigation --}}
                <div class="w-full md:w-1/4">
                    <nav class="space-y-1">
                        <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700'"
                            class="w-full text-left font-semibold px-4 py-3 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile Settings
                        </button>
                        <button @click="activeTab = 'orders'"
                            :class="activeTab === 'orders' ? 'bg-primary text-white' : 'hover:bg-gray-200 text-gray-700'"
                            class="w-full text-left font-semibold px-4 py-3 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Order History
                        </button>
                    </nav>
                </div>

                {{-- Content Area --}}
                <div class="w-full md:w-3/4 bg-white p-6 sm:p-8 rounded-lg shadow-md">
                    {{-- Profile Settings Tab --}}
                    <div x-show="activeTab === 'profile'" x-cloak>
                        <x-profile-settings :user="$user" />
                    </div>

                    {{-- Order History Tab --}}
                    <div x-show="activeTab === 'orders'" x-cloak>
                        <x-order-history :orders="$orders" />
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-landing-layout>