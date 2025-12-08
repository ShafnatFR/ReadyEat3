<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Recap - {{ $date }}</title>
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
                <h1 class="text-2xl font-bold text-gray-800">DAILY PRODUCTION RECAP</h1>
                <p class="text-gray-600">Date: {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Generated: {{ now()->format('H:i') }}</p>
            </div>
        </div>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-3 text-left">No</th>
                    <th class="border border-gray-300 p-3 text-left">Menu Item</th>
                    <th class="border border-gray-300 p-3 text-center">Total Quantity</th>
                    <th class="border border-gray-300 p-3 text-center">Checklist</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productionRecap as $index => $item)
                    <tr>
                        <td class="border border-gray-300 p-3 text-center w-12">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 p-3 font-medium">{{ $item['name'] }}</td>
                        <td class="border border-gray-300 p-3 text-center font-bold text-lg">{{ $item['quantity'] }}</td>
                        <td class="border border-gray-300 p-3 w-24"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border border-gray-300 p-8 text-center text-gray-500">No orders for this date
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-8 pt-8 border-t flex justify-between">
            <div class="text-center w-48">
                <p class="mb-16">Kitchen Staff</p>
                <p class="border-t border-gray-400 pt-1">(________________)</p>
            </div>
            <div class="text-center w-48">
                <p class="mb-16">Supervisor</p>
                <p class="border-t border-gray-400 pt-1">(________________)</p>
            </div>
        </div>

        <div class="mt-8 text-center no-print">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-lg">Print
                Recap</button>
            <button onclick="window.close()" class="ml-4 text-gray-600 hover:text-gray-800">Close</button>
        </div>
    </div>
    <script>window.onload = function () { window.print(); }</script>
</body>

</html>