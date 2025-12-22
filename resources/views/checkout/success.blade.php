<x-landing-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-600 mb-6">Your order has been placed successfully</p>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Invoice Number</p>
                    <p class="text-lg font-bold text-gray-900">{{ $order->invoice_code }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Pickup Date</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('d F Y') }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="text-2xl font-bold text-orange-600">Rp
                        {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">Order Status</p>
                    <span
                        class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
                <p class="text-sm text-orange-800">
                    <strong>Next Steps:</strong> Your payment is being verified. You will receive confirmation once
                    approved. Please bring your invoice code when picking up your order.
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ $whatsappUrl }}" target="_blank" id="whatsappBtn"
                    class="block w-full bg-green-600 text-white py-3 rounded-md font-semibold hover:bg-green-700 transition-colors">
                    Konfirmasi ke Admin via WhatsApp
                </a>
                <a href="{{ route('menus.index') }}" id="continueShoppingBtn"
                    class="block w-full bg-gray-400 text-gray-200 py-3 rounded-md font-semibold cursor-not-allowed opacity-60 pointer-events-none">
                    Continue Shopping (Locked)
                </a>
                <a href="{{ route('home') }}" id="backHomeBtn"
                    class="block w-full border border-gray-300 bg-gray-100 text-gray-400 py-3 rounded-md font-semibold cursor-not-allowed opacity-60 pointer-events-none">
                    Back to Home (Locked)
                </a>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p>Questions? Contact us at <a href="mailto:support@readyeat.com"
                        class="text-orange-600 hover:underline">support@readyeat.com</a></p>
            </div>
        </div>
    </div>

    <script>
        // Check if user already confirmed in this session
        const orderKey = 'wa_confirmed_{{ $order->id }}';
        
        if (localStorage.getItem(orderKey) === 'true') {
            unlockButtons();
        }

        // Listen for WhatsApp button click
        document.getElementById('whatsappBtn').addEventListener('click', function() {
            // Mark as confirmed in localStorage
            localStorage.setItem(orderKey, 'true');
            
            // Unlock buttons after a short delay (to ensure WA opened)
            setTimeout(unlockButtons, 1000);
        });

        function unlockButtons() {
            const continueBtn = document.getElementById('continueShoppingBtn');
            const homeBtn = document.getElementById('backHomeBtn');

            // Unlock Continue Shopping button
            continueBtn.classList.remove('bg-gray-400', 'text-gray-200', 'cursor-not-allowed', 'opacity-60', 'pointer-events-none');
            continueBtn.classList.add('bg-gray-900', 'text-white', 'hover:bg-gray-800');
            continueBtn.textContent = 'Continue Shopping';

            // Unlock Back to Home button
            homeBtn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'opacity-60', 'pointer-events-none');
            homeBtn.classList.add('hover:bg-gray-50', 'text-gray-700');
            homeBtn.textContent = 'Back to Home';
        }
    </script>
</x-landing-layout>