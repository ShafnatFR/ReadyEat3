{{-- Order Verification Tab --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto" x-data="{ selectedOrder: null, verifyModalOpen: false }">
    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center gap-4 flex-wrap">
        <h3 class="text-xl font-bold">Orders ({{ count($data['orders']) }})</h3>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-4">
            <input type="hidden" name="tab" value="verification">
            <input type="date" name="date_filter" value="{{ $data['dateFilter'] }}" class="bg-white dark:bg-gray-700 border rounded-md px-2 py-1 text-gray-900 dark:text-white">
            <select name="status_filter" class="bg-white dark:bg-gray-700 border rounded-md px-2 py-1 text-gray-900 dark:text-white">
                <option value="All Active" {{ $data['statusFilter'] === 'All Active' ? 'selected' : '' }}>All Active</option>
                <option value="Pending Verification" {{ $data['statusFilter'] === 'Pending Verification' ? 'selected' : '' }}>Pending Verification</option>
                <option value="Preparing" {{ $data['statusFilter'] === 'Preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="Ready for Pickup" {{ $data['statusFilter'] === 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="Completed" {{ $data['statusFilter'] === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Rejected" {{ $data['statusFilter'] === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="All" {{ $data['statusFilter'] === 'All' ? 'selected' : '' }}>All</option>
            </select>
            <button type="submit" class="bg-primary text-white px-3 py-1 rounded">Filter</button>
        </form>
    </div>
    
    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs">
            <tr>
                <th class="p-4">ID</th>
                <th class="p-4">Customer</th>
                <th class="p-4">Date</th>
                <th class="p-4">Total</th>
                <th class="p-4">Status</th>
                <th class="p-4">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($data['orders'] as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50" x-data="{ showDetails: false }">
                    <td class="p-4">{{ $order->invoice_code }}</td>
                    <td class="p-4">
                        <div>{{ $order->customer_name }}</div>
                        <div class="text-xs opacity-70">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="p-4">{{ $order->pickup_date }}</td>
                    <td class="p-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $order->status === 'Pending Verification' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'Preparing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'Ready for Pickup' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'Completed' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $order->status === 'Rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="p-4">
                        <button @click="showDetails = !showDetails" class="text-primary hover:underline">
                            {{ $order->status === 'Pending Verification' ? 'Verify' : 'Details' }}
                        </button>
                    </td>
                </tr>
                <tr x-show="showDetails" x-cloak>
                    <td colspan="6" class="p-6 bg-gray-50 dark:bg-gray-700/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold mb-3">Order Details</h4>
                                <div class="space-y-2">
                                    <p><strong>Items:</strong></p>
                                    <ul class="pl-4">
                                        @foreach($order->items as $item)
                                            <li>{{ $item->quantity }}x {{ $item->menu->name ?? 'Unknown' }}</li>
                                        @endforeach
                                    </ul>
                                    @if($order->notes)
                                        <p class="mt-3"><strong>Customer Notes:</strong></p>
                                        <p class="italic bg-amber-50 dark:bg-amber-900/30 p-2 rounded border-l-4 border-amber-400">{{ $order->notes }}</p>
                                    @endif
                                </div>
                                
                                @if($order->status === 'Pending Verification')
                                    <form method="POST" action="{{ route('admin.orders.accept', $order) }}" class="mt-4 space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block font-bold mb-1">Pickup Date</label>
                                            <input type="date" name="pickup_date" value="{{ $order->pickup_date }}" required class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block font-bold mb-1">Admin Notes</label>
                                            <textarea name="admin_note" rows="2" class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $order->admin_note }}</textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-orange-600">Accept Order</button>
                                            <button type="button" @click="$refs.rejectForm{{ $order->id }}.submit()" class="px-4 py-2 bg-orange-700 text-white rounded hover:bg-orange-800">Reject</button>
                                        </div>
                                    </form>
                                    <form x-ref="rejectForm{{ $order->id }}" method="POST" action="{{ route('admin.orders.reject', $order) }}" class="hidden">
                                        @csrf
                                    </form>
                                @endif
                            </div>
                            
                            <div>
                                <h4 class="font-bold mb-3">Payment Proof</h4>
                                @if($order->payment_proof)
                                    <img src="{{ Storage::url($order->payment_proof) }}" alt="Payment Proof" class="w-full rounded-lg shadow-md">
                                @else
                                    <div class="w-full aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <p class="text-gray-500 dark:text-gray-400">No payment proof uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">No orders found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
