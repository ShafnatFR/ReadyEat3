{{-- Order Verification Tab --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto">
    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center gap-4 flex-wrap">
        <h3 class="text-xl font-bold">Orders ({{ $data['orders']->total() }})</h3>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-4">
            <input type="hidden" name="tab" value="verification">
            <input type="date" name="date_filter" value="{{ $data['dateFilter'] }}"
                class="bg-white dark:bg-gray-700 border rounded-md px-2 py-1 text-gray-900 dark:text-white">
            <select name="status_filter"
                class="bg-white dark:bg-gray-700 border rounded-md px-2 py-1 text-gray-900 dark:text-white">
                <option value="All Active" {{ $data['statusFilter'] === 'All Active' ? 'selected' : '' }}>All Active
                </option>
                <option value="payment_pending" {{ $data['statusFilter'] === 'payment_pending' ? 'selected' : '' }}>
                    Payment Pending</option>
                <option value="ready_for_pickup" {{ $data['statusFilter'] === 'ready_for_pickup' ? 'selected' : '' }}>
                    Ready for Pickup</option>
                <option value="picked_up" {{ $data['statusFilter'] === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="cancelled" {{ $data['statusFilter'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="unpaid" {{ $data['statusFilter'] === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
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
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{ selectedOrderId: null }">
            @forelse($data['orders'] as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="p-4">{{ $order->invoice_code }}</td>
                    <td class="p-4">
                        <div>{{ $order->customer_name }}</div>
                        <div class="text-xs opacity-70">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}</td>
                    <td class="p-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $order->status === 'payment_pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $order->status === 'ready_for_pickup' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $order->status === 'picked_up' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $order->status === 'unpaid' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td class="p-4">
                        <button @click="selectedOrderId = {{ $order->id }}"
                            class="text-primary hover:underline font-medium">
                            Details
                        </button>
                    </td>
                </tr>

                <template x-teleport="body">
                    <div x-show="selectedOrderId === {{ $order->id }}" x-cloak @click="selectedOrderId = null"
                        @keydown.escape.window="selectedOrderId = null"
                        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" role="dialog"
                        aria-modal="true">

                        <div @click.stop
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
                            x-data="{ 
                                        imageZoom: false,
                                        status: '{{ $order->status }}',
                                        message: '',
                                        phone: '{{ $order->customer_phone }}',

                                        init() {
                                            this.updateMessage();
                                            $watch('status', value => this.updateMessage());
                                        },

                                        updateMessage() {
                                            const templates = {
                                                'payment_pending': 'Halo Kak {{ addslashes($order->customer_name) }}, mohon konfirmasi untuk pesanan {{ $order->invoice_code }}. Apakah pembayaran sudah dilakukan? Mohon kirim bukti transfer yang jelas ya. Terima kasih! 🙏',
                                                'ready_for_pickup': 'Halo Kak {{ addslashes($order->customer_name) }}, pesanan {{ $order->invoice_code }} SUDAH SIAP diambil ya! Ditunggu kedatangannya. 🍽️',
                                                'picked_up': 'Halo Kak {{ addslashes($order->customer_name) }}, terima kasih sudah order {{ $order->invoice_code }}. Selamat menikmati! Ditunggu order selanjutnya ya. ✨',
                                                'cancelled': 'Halo Kak {{ addslashes($order->customer_name) }}, mohon maaf pesanan {{ $order->invoice_code }} belum dapat kami proses karena BUKTI PEMBAYARAN TIDAK JELAS / TIDAK SESUAI. Mohon kirim ulang bukti yang benar ya. Terima kasih. 🙏',
                                                'unpaid': 'Halo Kak {{ addslashes($order->customer_name) }}, pesanan {{ $order->invoice_code }} belum dibayar. Mohon selesaikan pembayaran agar pesanan dapat diproses.'
                                            };
                                            this.message = templates[this.status] || '';
                                        },

                                        get waLink() {
                                            // Remove non-numeric chars for phone link
                                            let cleanPhone = this.phone.replace(/\D/g, '');
                                            // Ensure ID format (62...)
                                            if(cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.substring(1);

                                            return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(this.message)}`;
                                        }
                                    }">
                            {{-- Modal Header --}}
                            <div
                                class="p-6 border-b dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 z-10">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    Verify Order: {{ $order->invoice_code }}
                                </h2>
                                <button @click="selectedOrderId = null"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-3xl leading-none">
                                    &times;
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Left Column: Order Details & Form --}}
                                <div>
                                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Order Details</h3>
                                    <div class="space-y-3 text-gray-900 dark:text-gray-200 mb-6">
                                        <div><strong class="text-gray-800 dark:text-gray-300">Customer:</strong>
                                            {{ $order->customer_name }}</div>
                                        <div><strong class="text-gray-800 dark:text-gray-300">Phone:</strong>
                                            {{ $order->customer_phone }}</div>
                                        <div><strong class="text-gray-800 dark:text-gray-300">Total:</strong> Rp
                                            {{ number_format($order->total_price, 0, ',', '.') }}
                                        </div>

                                        <div class="space-y-2">
                                            <strong class="text-gray-800 dark:text-gray-300">Items:</strong>
                                            @foreach($order->items as $item)
                                                <div
                                                    class="pl-4 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                                    {{ $item->quantity }}x {{ $item->menu->name ?? 'Unknown' }}
                                                    <span class="float-right font-medium">Rp
                                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($order->notes)
                                            <div class="pt-2">
                                                <strong class="text-gray-800 dark:text-gray-300">Customer Notes:</strong>
                                                <p
                                                    class="pl-4 italic text-gray-700 dark:text-gray-300 bg-amber-50 dark:bg-amber-900/30 p-2 rounded-md border-l-4 border-amber-400 mt-1">
                                                    {{ $order->notes }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Update Form --}}
                                    <form method="POST" action="{{ route('admin.orders.accept', $order) }}"
                                        class="space-y-4">
                                        @csrf
                                        <div>
                                            <label
                                                class="block font-bold mb-1 text-gray-900 dark:text-gray-300">Status:</label>
                                            <select name="status" x-model="status"
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary">
                                                <option value="payment_pending">Payment Pending</option>
                                                <option value="ready_for_pickup">Ready for Pickup</option>
                                                <option value="picked_up">Picked Up</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-bold mb-1 text-gray-900 dark:text-gray-300">Tanggal
                                                Pengambilan:</label>
                                            <input type="date" name="pickup_date" value="{{ $order->pickup_date }}" required
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary">
                                        </div>

                                        <div>
                                            <label class="block font-bold mb-1 text-gray-900 dark:text-gray-300">Catatan
                                                Admin (Internal):</label>
                                            <textarea name="admin_note" rows="2"
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="e.g., Customer agreed to move pickup date.">{{ $order->admin_note }}</textarea>
                                        </div>

                                        {{-- Custom Message for Customer --}}
                                        <div>
                                            <label class="block font-bold mb-1 text-gray-900 dark:text-gray-300">Pesan
                                                WhatsApp Customer:</label>
                                            <textarea x-model="message" rows="3"
                                                class="w-full p-2 border border-green-300 dark:border-green-700 rounded-md bg-green-50 dark:bg-green-900/10 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                                placeholder="Pesan untuk customer..."></textarea>
                                            <p class="text-xs text-gray-500 mt-1">Pesan ini akan dikirim saat Anda klik
                                                tombol Chat WA.</p>
                                        </div>

                                        <div class="flex gap-2 pt-4">
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-primary text-white rounded-md font-semibold hover:bg-orange-600 transition-colors">
                                                Update Order
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Right Column: Payment Proof --}}
                                <div>
                                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Payment Proof</h3>
                                    @if($order->payment_proof)
                                        <div class="relative">
                                            <img src="{{ Storage::url($order->payment_proof) }}" alt="Payment Proof"
                                                @click="imageZoom = true"
                                                class="w-full rounded-lg shadow-md cursor-pointer hover:opacity-90 transition-opacity">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Click to zoom
                                            </p>
                                        </div>

                                        {{-- Image Zoom Modal --}}
                                        <div x-show="imageZoom" x-cloak @click="imageZoom = false"
                                            class="fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="relative max-w-6xl w-full" @click.stop>
                                                <button @click="imageZoom = false"
                                                    class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl leading-none">
                                                    &times;
                                                </button>
                                                <img src="{{ Storage::url($order->payment_proof) }}" alt="Payment Proof Zoomed"
                                                    class="w-full h-auto rounded-lg shadow-2xl">
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="w-full aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">No payment proof uploaded</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div
                                class="p-6 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700 flex flex-wrap justify-between items-center gap-4">
                                <a :href="waLink" target="_blank" rel="noopener noreferrer"
                                    class="px-4 py-2 bg-green-500 text-white rounded-md font-semibold hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                    Chat WA
                                </a>

                                <div class="flex gap-2">
                                    <button @click="selectedOrderId = null" type="button"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-md font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                                        Close
                                    </button>

                                    <a :href="`{{ url('admin/orders') }}/${selectedOrderId}/invoice`" target="_blank"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                        Print Invoice
                                    </a>

                                    @if($order->status === 'payment_pending')
                                        <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="inline"
                                            onsubmit="return confirm('Are you sure you want to reject this order?')">
                                            @csrf
                                            <input type="hidden" name="admin_note" value="Order rejected by admin">
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">No orders found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination Controls --}}
    @include('admin.components.pagination', ['items' => $data['orders'], 'perPage' => $data['perPage'] ?? 20])
</div>