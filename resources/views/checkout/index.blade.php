<x-landing-layout>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            <div class="space-y-4">
                @foreach($cart as $id => $details)
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}"
                                class="w-16 h-16 rounded object-cover" />
                            <div>
                                <p class="font-semibold text-gray-800">{{ $details['name'] }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $details['quantity'] }}</p>
                            </div>
                        </div>
                        <p class="font-semibold">Rp
                            {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 space-y-2 text-gray-700">
                <div class="flex justify-between"><span>Subtotal</span><span>Rp
                        {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Shipping</span><span>Rp
                        {{ number_format($shipping, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-lg text-gray-900 pt-2 border-t">
                    <span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Payment Information</h2>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">
                    <p class="text-sm font-semibold mb-2">Please fix the following errors:</p>
                    @foreach($errors->all() as $error)
                        <p class="text-sm">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Date</label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date') }}" min="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">{{ old('notes') }}</textarea>
                </div>

                <div class="mb-6 p-6 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-4 text-center">Scan QRIS Code to Pay</h3>
                    <div class="flex justify-center mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Pay-Rp{{ $total }}"
                            alt="QRIS Code" class="w-48 h-48 rounded-lg border-2 border-gray-300" />
                    </div>
                    <p class="text-center text-sm text-gray-600 mb-4">Scan QR code above using your e-wallet app</p>

                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof *</label>
                    <input type="file" name="payment_proof" accept="image/*" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" />
                    <p class="text-xs text-gray-500 mt-1">Upload screenshot of your payment confirmation (max 2MB)</p>
                </div>

                <button type="submit"
                    class="w-full bg-gray-900 text-white py-3 rounded-md font-semibold hover:bg-gray-800 transition-colors">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</x-landing-layout>