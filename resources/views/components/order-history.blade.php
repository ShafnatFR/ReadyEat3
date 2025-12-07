@props(['orders'])

<div>
    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">Order History</h3>

    @if($orders->isEmpty())
        <div class="text-center py-10">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="text-gray-600 text-lg">You have no orders yet.</p>
            <a href="{{ route('menus.index') }}"
                class="inline-block mt-4 bg-primary text-white px-6 py-2 rounded-md hover:bg-orange-600 transition-colors">
                Browse Menu
            </a>
        </div>
    @else
        <div class="space-y-4" x-data="{ expandedOrderId: null }">
            @foreach($orders as $order)
                <div class="border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    {{-- Order Header (Clickable) --}}
                    <button @click="expandedOrderId = expandedOrderId === '{{ $order->id }}' ? null : '{{ $order->id }}'"
                        class="w-full text-left p-4 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition-colors"
                        aria-expanded="false" :aria-expanded="expandedOrderId === '{{ $order->id }}'">

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-center">
                            <div>
                                <p class="text-sm text-gray-500">Order ID</p>
                                <p class="font-semibold text-primary truncate">{{ $order->invoice_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pickup Date</p>
                                <p class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total</p>
                                <p class="font-semibold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex justify-between items-center">
                                @php
                                    $statusClasses = [
                                        'payment_pending' => 'bg-yellow-100 text-yellow-800',
                                        'payment_rejected' => 'bg-red-100 text-red-800',
                                        'ready_for_pickup' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $statusLabels = [
                                        'payment_pending' => 'Pending',
                                        'payment_rejected' => 'Rejected',
                                        'ready_for_pickup' => 'Ready',
                                        'completed' => 'Completed',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                <svg :class="expandedOrderId === '{{ $order->id }}' ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-gray-600 transition-transform" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Order Details (Expandable) --}}
                    <div x-show="expandedOrderId === '{{ $order->id }}'" x-collapse
                        class="p-4 border-t border-gray-200 bg-gray-50">

                        <h4 class="font-semibold text-lg mb-3 text-gray-800">Items Ordered:</h4>
                        <ul class="space-y-3">
                            @foreach($order->items as $item)
                                <li class="flex items-center space-x-4">
                                    <img src="{{ $item->menu->image ?? 'https://via.placeholder.com/100' }}"
                                        alt="{{ $item->menu->name ?? 'Product' }}"
                                        class="w-16 h-16 rounded-md object-cover shadow-sm">
                                    <div class="flex-grow">
                                        <p class="font-semibold text-gray-700">{{ $item->menu->name ?? 'Unknown Product' }}</p>
                                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-800">Rp
                                        {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>

                        @if($order->notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm font-medium text-gray-700">Notes:</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>