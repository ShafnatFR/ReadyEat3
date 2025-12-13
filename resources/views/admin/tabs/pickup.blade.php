{{-- Pickup Counter Tab --}}
<div x-data="{ 
    searchQuery: '{{ request('search') }}', 
    period: '{{ request('pickup_period', 'today') }}',
    showAdvanced: {{ request('pickup_period') === 'custom' ? 'true' : 'false' }} 
}">
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('admin.dashboard') }}">
            <input type="hidden" name="tab" value="pickup">

            <div class="flex flex-col gap-4">
                {{-- Top Row: Search & Quick Filters --}}
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-end">
                    {{-- Search Input --}}
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search
                            Order</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" name="search" x-model="searchQuery" placeholder="Name or Order ID"
                                class="w-full pl-10 p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                        </div>
                    </div>

                    {{-- Quick Filters Buttons --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" name="pickup_period" value="today"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border"
                            :class="period === 'today' 
                                ? 'bg-primary text-white border-primary shadow-sm' 
                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'">
                            Hari Ini
                        </button>
                        <button type="submit" name="pickup_period" value="week"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border"
                            :class="period === 'week' 
                                ? 'bg-primary text-white border-primary shadow-sm' 
                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'">
                            Minggu Ini
                        </button>
                        <button type="submit" name="pickup_period" value="month"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border"
                            :class="period === 'month' 
                                ? 'bg-primary text-white border-primary shadow-sm' 
                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'">
                            Bulan Ini
                        </button>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" @click="showAdvanced = !showAdvanced"
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-2"
                            :class="showAdvanced ? 'bg-gray-100 dark:bg-gray-600' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                </path>
                            </svg>
                            <span class="hidden sm:inline">Advanced</span>
                        </button>

                        <a href="{{ route('admin.pickup.print') }}?date={{ request('pickup_period') === 'today' || !request('pickup_period') ? date('Y-m-d') : '' }}"
                            target="_blank"
                            class="p-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-sm"
                            title="Print List (Today)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Advanced Filters (Collapsible) --}}
                <div x-show="showAdvanced" x-collapse x-cloak
                    class="pt-4 mt-2 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-4 animate-fade-in-down">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Date
                            Range</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="flex-1 p-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                            <span class="text-gray-500">to</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="flex-1 p-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" name="pickup_period" value="custom"
                            class="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-orange-600 shadow-md transition-colors w-full md:w-auto">
                            Apply Range Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if(count($pickupOrders) === 0)
        <div
            class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
            <p class="text-xl font-medium text-gray-500 dark:text-gray-400">No orders found matching criteria.</p>
            <p class="text-gray-400 text-sm mt-1">Try changing the date filter or search keyword.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pickupOrders as $order)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col justify-between border-2 border-gray-100 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-all duration-200">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight mb-1">
                                    {{ $order->customer_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    {{ $order->customer_phone }}
                                </p>
                                <p
                                    class="text-xs text-gray-400 font-mono mt-1 bg-gray-100 dark:bg-gray-700 inline-block px-2 py-0.5 rounded">
                                    {{ $order->invoice_code }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div
                                    class="px-3 py-1 text-xs font-bold text-white rounded-full bg-green-500 shadow-sm flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    READY
                                </div>
                            </div>
                        </div>

                        {{-- Date Badge --}}
                        <div class="mb-4">
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold border border-blue-100 dark:border-blue-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ \Carbon\Carbon::parse($order->pickup_date)->translatedFormat('l, d M Y') }}
                            </span>
                        </div>

                        <div class="border-t border-b border-gray-100 dark:border-gray-700 py-3 mb-4">
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300 text-sm">
                                @foreach($order->items as $item)
                                    <li class="flex justify-between">
                                        <span>{{ $item->quantity }}x {{ $item->menu->name ?? 'Unknown Item' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex justify-between items-center text-gray-900 dark:text-white">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Amount</span>
                            <span class="text-lg font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 p-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                        <form method="POST" action="{{ route('admin.orders.complete', $order) }}" class="flex-1"
                            onsubmit="return confirm('Tandai order ini sebagai sudah diambil?')">
                            @csrf
                            <button type="submit"
                                class="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-bold hover:bg-orange-600 transition-all shadow-md active:transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                Mark Picked Up
                            </button>
                        </form>
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                            class="bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-600 px-3 rounded-lg flex items-center justify-center hover:bg-blue-50 dark:hover:bg-gray-600 transition-colors"
                            title="Print Invoice">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>