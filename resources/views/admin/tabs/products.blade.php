{{-- Products Management Tab --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto"
    x-data="{ editModalOpen: false, editingProduct: null, formData: {} }">
    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-xl font-bold">Products ({{ count($products) }})</h3>
        <button @click="editingProduct = null; formData = {}; editModalOpen = true"
            class="bg-primary text-white px-3 py-1 rounded hover:bg-orange-600">
            Add New Product
        </button>
    </div>

    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs">
            <tr>
                <th class="p-4">Name</th>
                <th class="p-4">Price</th>
                <th class="p-4">Category</th>
                <th class="p-4">Daily Quota</th>
                <th class="p-4">Available</th>
                <th class="p-4">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($products as $product)
                <tr class="{{ !$product->is_available ? 'opacity-60 bg-gray-100 dark:bg-gray-700/30' : '' }}">
                    <td class="p-4 font-semibold">{{ $product->name }}</td>
                    <td class="p-4">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="p-4">{{ $product->category }}</td>
                    <td class="p-4">{{ $product->daily_limit }}</td>
                    <td class="p-4">
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                            @csrf
                            <label
                                class="relative inline-flex items-center cursor-pointer hover:opacity-80 transition-opacity"
                                title="Click to toggle availability">
                                <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $product->is_available ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary">
                                </div>
                            </label>
                        </form>
                    </td>
                    <td class="p-4 flex gap-2">
                        <button
                            @click="editingProduct = {{ $product->id }}; formData = {{ json_encode($product) }}; editModalOpen = true"
                            class="text-blue-500 hover:underline">Edit</button>
                        <form method="POST" action="{{ route('admin.products.delete', $product) }}"
                            onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">No products yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Product Edit Modal --}}
    <div x-show="editModalOpen" x-cloak
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4">
        <div @click.away="editModalOpen = false"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form
                :action="editingProduct ? '{{ url('admin/products') }}/' + editingProduct : '{{ route('admin.products.store') }}'"
                method="POST">
                @csrf
                <template x-if="editingProduct">
                    @method('PUT')
                </template>

                <div class="p-6 border-b dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white"
                        x-text="editingProduct ? 'Edit Product' : 'Add New Product'"></h2>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" :value="formData.name || ''" required
                            class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" :value="formData.description || ''" rows="3"
                            class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Price *</label>
                            <input type="number" name="price" :value="formData.price || ''" required
                                class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Daily Quota
                                *</label>
                            <input type="number" name="daily_limit" :value="formData.daily_limit || ''" required
                                class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Image URL *</label>
                        <input type="text" name="image_url" :value="formData.image || ''" required
                            class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Category *</label>
                            <select name="category"
                                class="w-full p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="Katering" :selected="formData.category === 'Katering'">Katering</option>
                                <option value="Instant" :selected="formData.category === 'Instant'">Instant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-900 dark:text-gray-300 mb-1">Available</label>
                            <label class="relative inline-flex items-center cursor-pointer mt-2">
                                <input type="hidden" name="is_available" value="0">
                                <input type="checkbox" name="is_available" value="1"
                                    :checked="formData.is_available ?? true" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all">
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Yes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700 flex justify-end gap-4">
                    <button type="button" @click="editModalOpen = false"
                        class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-md hover:bg-orange-600">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>