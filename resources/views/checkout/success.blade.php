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
                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('d F Y') }}
                    </p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="text-2xl font-bold text-orange-600">Rp
                        {{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
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
                    class="block w-full bg-green-600 text-white py-3 rounded-md font-semibold hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
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

            {{-- Auto-redirect to WhatsApp after 3 seconds --}}
            <script>
                setTimeout(function () {
                    window.open('{{ $whatsappUrl }}', '_blank');
                }, 3000);
            </script>

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