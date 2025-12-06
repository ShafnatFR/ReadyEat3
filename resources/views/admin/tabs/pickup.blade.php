{{-- Pickup Counter Tab --}}
<div x-data="{ searchQuery: '', pickupDate: '{{ request()->get('pickup_date', date('Y-m-d')) }}' }">
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="tab" value="pickup">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" :value="searchQuery" placeholder="Name or Order ID"
                    class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Pickup Date</label>
                <div class="flex gap-2">
                    <input type="date" name="pickup_date" :value="pickupDate"
                        class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button type="submit" class="px-4 bg-primary text-white rounded hover:bg-orange-600">Filter</button>
                </div>
            </div>
        </form>
    </div>

    @if(count($pickupOrders) === 0)
        <div class="text-center py-16">
            <p class="text-xl text-gray-500 dark:text-gray-400">No orders ready for pickup matching your search.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pickupOrders as $order)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col justify-between border-2 border-gray-100 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-colors">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_phone }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-500 font-mono mt-1">{{ $order->invoice_code }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="px-4 py-1 text-sm font-bold text-white rounded-full bg-green-500">READY</div>
                            </div>
                        </div>
                        <ul class="mb-6 space-y-1 text-gray-700 dark:text-gray-300">
                            @foreach($order->items as $item)
                                <li>{{ $item->quantity }}x {{ $item->menu->name ?? 'Unknown' }}</li>
                            @endforeach
                        </ul>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mb-4">Total: Rp
                            {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.complete', $order) }}">
                        @csrf
                        <button type="submit"
                            class="w-full bg-primary text-white py-4 rounded-lg text-lg font-bold hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Mark as Picked Up
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>