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
                <a href="{{ route('menus.index') }}"
                    class="block w-full bg-gray-900 text-white py-3 rounded-md font-semibold hover:bg-gray-800 transition-colors">
                    Continue Shopping
                </a>
                <a href="{{ route('home') }}"
                    class="block w-full border border-gray-300 text-gray-700 py-3 rounded-md font-semibold hover:bg-gray-50 transition-colors">
                    Back to Home
                </a>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p>Questions? Contact us at <a href="mailto:support@readyeat.com"
                        class="text-orange-600 hover:underline">support@readyeat.com</a></p>
            </div>
        </div>
    </div>
</x-landing-layout>