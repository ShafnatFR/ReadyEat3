<x-landing-layout>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <nav class="flex mb-8 text-sm text-gray-500">
            <a href="{{ route('menus.index') }}" class="hover:text-orange-600">Menu</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-semibold">Checkout</span>
        </nav>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Konfirmasi Pesanan</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-2 space-y-6">
                
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat masalah pada pesanan Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
                    @csrf
                    
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                            Jadwal Pengambilan
                        </h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tanggal</label>
                            <input type="date" name="pickup_date" 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ old('pickup_date') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 shadow-sm" required>
                            <p class="text-xs text-gray-500 mt-2">*Pesanan untuk hari yang sama maksimal sebelum jam 10.00 WIB (Tergantung kebijakan dapur).</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                            Pembayaran (QRIS)
                        </h2>

                        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 mb-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" alt="QRIS" class="w-48 h-48 mb-2">
                            <p class="font-bold text-gray-800">Scan QRIS di atas</p>
                            <p class="text-sm text-gray-500">a.n Katering Kampus ReadyEat</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                            <input type="file" name="payment_proof" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                            Catatan Tambahan (Opsional)
                        </h2>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 shadow-sm" placeholder="Contoh: Sambal dipisah ya, jangan pakai sendok plastik.">{{ old('notes') }}</textarea>
                    </div>

                </form>
            </div>

            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm sticky top-24">
                    <h2 class="text-lg font-bold mb-4 text-gray-900">Ringkasan</h2>
                    
                    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                        @foreach ($cart as $item)
                        <div class="flex justify-between items-start text-sm">
                            <div>
                                <p class="font-medium text-gray-800">{{ $item['name'] }}</p>
                                <p class="text-gray-500 text-xs">x{{ $item['quantity'] }}</p>
                            </div>
                            <span class="font-semibold text-gray-700">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 mb-6">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Biaya Layanan</span>
                            <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-dashed">
                            <span>Total Bayar</span>
                            <span class="text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button onclick="document.getElementById('checkout-form').submit()" 
                            class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-orange-700 transition transform active:scale-95">
                        Bayar & Selesaikan
                    </button>
                    
                    <p class="text-xs text-center text-gray-400 mt-4">
                        Dengan mengklik tombol di atas, pesanan Anda akan diproses oleh admin.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-landing-layout>