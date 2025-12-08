<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup List - {{ $date }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 p-8 print:p-0 print:bg-white">
    <div class="max-w-4xl mx-auto bg-white p-8 shadow-lg print:shadow-none print:w-full">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">DAILY PICKUP LIST</h1>
                <p class="text-gray-600">Date: {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Generated: {{ now()->format('H:i') }}</p>
            </div>
        </div>

        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-center">No</th>
                    <th class="border border-gray-300 p-2 text-left">Invoice</th>
                    <th class="border border-gray-300 p-2 text-left">Customer</th>
                    <th class="border border-gray-300 p-2 text-left">Items</th>
                    <th class="border border-gray-300 p-2 text-center">Status</th>
                    <th class="border border-gray-300 p-2 text-center w-32">Signature</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pickupOrders as $index => $order)
                    <tr>
                        <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 p-2 font-mono">{{ $order->invoice_code }}</td>
                        <td class="border border-gray-300 p-2">
                            <div class="font-bold">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <ul class="list-disc list-inside">
                                @foreach($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->menu->name ?? 'Unknown' }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="border border-gray-300 p-2 text-center">
                            <span
                                class="px-2 py-1 rounded text-xs font-bold border
                                {{ $order->status === 'picked_up' ? 'border-green-500 text-green-700' : 'border-blue-500 text-blue-700' }}">
                                {{ $order->status === 'picked_up' ? 'DONE' : 'READY' }}
                            </span>
                        </td>
                        <td class="border border-gray-300 p-2"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border border-gray-300 p-8 text-center text-gray-500">No pickups scheduled
                            for this date</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-8 text-center no-print">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-lg">Print
                List</button>
            <button onclick="window.close()" class="ml-4 text-gray-600 hover:text-gray-800">Close</button>
        </div>
    </div>
    <script>window.onload = function () { window.print(); }</script>
</body>

</html>