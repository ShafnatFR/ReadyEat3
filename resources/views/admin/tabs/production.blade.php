{{-- Production Recap Tab --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto" id="production-recap">
    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center print:hidden">
        <h3 class="text-xl font-bold">Daily Production</h3>
        <div class="flex gap-4">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-2">
                <input type="hidden" name="tab" value="production">
                <input type="date" name="production_date" value="{{ $productionDate }}"
                    class="bg-white dark:bg-gray-700 border rounded-md px-2 py-1 text-gray-900 dark:text-white">
                <button type="submit" class="bg-primary text-white px-3 py-1 rounded">Load</button>
            </form>
            <button onclick="window.open('{{ route('admin.production.print') }}?date={{ $productionDate }}', '_blank')"
                class="bg-primary text-white px-3 py-1 rounded flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Print Recap
            </button>
        </div>
    </div>

    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs">
            <tr>
                <th class="p-4">Menu</th>
                <th class="p-4">Quantity</th>
                <th class="p-4">Daily Quota</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($productionRecap as $item)
                <tr class="border-b dark:border-gray-700">
                    <td class="p-4 font-semibold">{{ $item['name'] }}</td>
                    <td class="p-4 font-bold text-primary">{{ $item['quantity'] }}</td>
                    <td class="p-4 opacity-70">{{ $item['quota'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-8 text-center text-gray-500">No orders for this date</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>