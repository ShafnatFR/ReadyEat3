{{-- Dashboard/Reporting Tab --}}
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Total Revenue</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp
                {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Total Orders</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['totalOrders'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Unique Customers</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['uniqueCustomers'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Avg Order Value</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp
                {{ number_format($stats['avgOrderValue'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter Controls --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">Filters</h3>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="tab" value="dashboard">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                <select name="filter_type"
                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white">
                    <option value="daily" {{ $stats['filterType'] === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $stats['filterType'] === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $stats['filterType'] === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $stats['filterType'] === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Date</label>
                @if($stats['filterType'] === 'yearly')
                    <input type="number" name="selected_date" value="{{ $stats['selectedDate'] }}" min="2020" max="2099"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white">
                @elseif($stats['filterType'] === 'weekly')
                    <input type="week" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white">
                @elseif($stats['filterType'] === 'monthly')
                    <input type="month" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white">
                @else
                    <input type="date" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white">
                @endif
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Products --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Top 5 Products</h3>
            @if(count($stats['topProducts']) > 0)
                <div class="space-y-3">
                    @foreach($stats['topProducts'] as $product)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 dark:text-gray-300">{{ $product['name'] }}</span>
                            <span class="text-primary font-bold">{{ $product['quantity'] }} sold</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No data available</p>
            @endif
        </div>

        {{-- Category Distribution --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Category Sales</h3>
            @if(count($stats['categoryDistribution']) > 0)
                <div class="space-y-3">
                    @foreach($stats['categoryDistribution'] as $cat)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 dark:text-gray-300">{{ $cat['label'] }}</span>
                            <span class="text-primary font-bold">{{ $cat['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No data available</p>
            @endif
        </div>
    </div>

    {{-- Revenue Trend --}}
    @if(count($stats['revenueTrend']) > 1)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
            <div class="h-64 flex items-end space-x-2">
                @php
                    $maxValue = max(array_column($stats['revenueTrend'], 'value'));
                @endphp
                @foreach($stats['revenueTrend'] as $point)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-primary rounded-t"
                            style="height: {{ $maxValue > 0 ? ($point['value'] / $maxValue * 100) . '%' : '0%' }}; min-height: 2px;">
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>