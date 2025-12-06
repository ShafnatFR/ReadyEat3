{{-- Pagination Controls Component --}}
<div
    class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-b-lg">
    {{-- Per Page Selector --}}
    <div class="flex items-center gap-2">
        <label for="per-page-select" class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
        <select id="per-page-select" onchange="updatePerPage(this.value)"
            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-1 pl-3 pr-10 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
            <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
        </select>
        <span class="text-sm text-gray-700 dark:text-gray-300">
            entries (Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
            total)
        </span>
    </div>

    {{-- Pagination Links --}}
    <div>
        {{ $items->appends(request()->except('page'))->links() }}
    </div>
</div>

<script>
    function updatePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1'); // Reset to page 1 when changing per_page
        window.location.href = url.toString();
    }
</script>