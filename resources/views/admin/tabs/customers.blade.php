{{-- Customer Management Tab --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto" x-data="{ selectedCustomerId: null }">
    <div class="p-4 border-b dark:border-gray-700">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Customer Management ({{ $customers['customers']->total() }})</h3>
        
        {{-- Search and Filter Form --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-3">
            <input type="hidden" name="tab" value="customers">
            
            {{-- Search Input --}}
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by name or phone..."
                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            
            {{-- Sort By Field --}}
            <select name="sort_by" 
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                <option value="spending" {{ request('sort_by', 'spending') == 'spending' ? 'selected' : '' }}>Sort: Spending</option>
                <option value="orders" {{ request('sort_by') == 'orders' ? 'selected' : '' }}>Sort: Orders</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort: Name</option>
                <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Sort: First Seen</option>
            </select>
            
            {{-- Sort Direction Buttons --}}
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button type="submit" name="direction" value="desc" 
                    class="px-3 py-1.5 rounded {{ request('direction', 'desc') == 'desc' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <button type="submit" name="direction" value="asc" 
                    class="px-3 py-1.5 rounded {{ request('direction') == 'asc' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Reset Button --}}
            @if(request('search') || request('sort_by') != 'spending' || request('direction') == 'asc')
                <a href="{{ route('admin.dashboard', ['tab' => 'customers']) }}" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider">
            <tr>
                <th class="p-4">Customer</th>
                <th class="p-4">Total Orders</th>
                <th class="p-4">Total Spent</th>
                <th class="p-4">First Seen</th>
                <th class="p-4"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($customers['customers'] as $customer)
                <tr @click="selectedCustomerId = (selectedCustomerId === '{{ $customer['phone'] }}' ? null : '{{ $customer['phone'] }}')"
                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="p-4">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer['phone'] }}</div>
                    </td>
                    <td class="p-4 text-gray-900 dark:text-white">{{ $customer['totalOrders'] }}</td>
                    <td class="p-4 text-gray-900 dark:text-white font-bold">Rp
                        {{ number_format($customer['totalSpent'], 0, ',', '.') }}</td>
                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $customer['firstSeen'] }}</td>
                    <td class="p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-gray-400 transition-transform"
                            :class="selectedCustomerId === '{{ $customer['phone'] }}' ? 'rotate-90' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </td>
                </tr>
                <tr x-show="selectedCustomerId === '{{ $customer['phone'] }}'" x-cloak>
                    <td colspan="5" class="p-6 bg-gray-50 dark:bg-gray-700/30">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-3">Order History</h4>
                        <div class="space-y-2">
                            @foreach($customer['orders'] as $order)
                                <div
                                    class="p-3 border dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 flex justify-between items-center">
                                    <div>
                                        <span
                                            class="font-mono text-sm text-gray-600 dark:text-gray-400">{{ $order->invoice_code }}</span>
                                        <span
                                            class="text-sm text-gray-500 dark:text-gray-400 ml-2">({{ $order->pickup_date }})</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 dark:text-white">Rp
                                            {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        <div
                                            class="text-xs {{ $order->status === 'picked_up' ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $order->status }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">No customers yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Pagination Controls --}}
    @include('admin.components.pagination', ['items' => $customers['customers'], 'perPage' => $customers['perPage'] ?? 20])
</div>
