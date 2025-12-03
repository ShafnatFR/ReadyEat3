<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - {{ config('app.name', 'ReadyEat') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Instrument Sans', 'sans-serif'],
                        },
                        colors: {
                            primary: '#FF2D20',
                        }
                    }
                }
            }
        </script>
    @endif
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] font-sans antialiased">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <aside
            class="w-full lg:w-64 bg-white dark:bg-[#161615] border-b lg:border-b-0 lg:border-r border-[#e3e3e0] dark:border-[#3E3E3A] p-6 flex flex-col justify-between shrink-0">
            <div>
                <div class="flex items-center gap-2 mb-8">
                    <svg class="w-8 h-8 text-[#FF2D20]" viewBox="0 0 60 60" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.5 10C8.5 9.17157 9.17157 8.5 10 8.5H50C50.8284 8.5 51.5 9.17157 51.5 10V50C51.5 50.8284 50.8284 51.5 50 51.5H10C9.17157 51.5 8.5 50.8284 8.5 50V10Z"
                            fill="currentColor" />
                    </svg>
                    <span class="text-xl font-bold tracking-tight">Admin Panel</span>
                </div>

                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2.5 bg-[#FF2D20]/10 text-[#FF2D20] rounded-lg font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Pesanan
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        Menu & Stok
                    </a>
                </nav>
            </div>

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg w-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-10 overflow-y-auto">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-white">Selamat Pagi,
                        {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan operasional katering hari ini.
                    </p>
                </div>
                <div class="text-right hidden md:block">
                    <p class="text-sm font-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                    <p class="text-xs text-gray-500">Status Toko: <span class="text-green-600 font-semibold">Buka</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div
                    class="bg-white dark:bg-[#161615] p-6 rounded-xl border border-red-100 dark:border-red-900/30 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <svg class="w-24 h-24 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Butuh Verifikasi WA</p>
                    <h2 class="text-4xl font-bold text-[#1b1b18] dark:text-white mt-2">{{ $verificationNeeded }}</h2>
                    <p class="text-xs text-red-500 mt-2 font-medium flex items-center gap-1">
                        @if($verificationNeeded > 0)
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            Segera proses!
                        @else
                            <span class="text-green-500">Semua aman</span>
                        @endif
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-[#161615] p-6 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-sm">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Siap Diambil Hari Ini</p>
                    <h2 class="text-4xl font-bold text-[#1b1b18] dark:text-white mt-2">{{ $readyPickupToday }}</h2>
                    <p class="text-xs text-gray-500 mt-2">Pesanan status 'Ready' tanggal {{ date('d/m') }}</p>
                </div>

                <div
                    class="bg-white dark:bg-[#161615] p-6 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-sm">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimasi Omset Hari Ini</p>
                    <h2 class="text-4xl font-bold text-[#1b1b18] dark:text-white mt-2">
                        Rp {{ number_format($todaysRevenue, 0, ',', '.') }}
                    </h2>
                    <p class="text-xs text-green-600 mt-2 font-medium">Dr. Pesanan Masuk</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">

                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-white mb-4">Mode Operasional</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="#"
                            class="group relative flex flex-col p-6 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-950/30 dark:to-orange-900/10 border border-orange-200 dark:border-orange-800/30 rounded-xl hover:shadow-md transition-all">
                            <div
                                class="mb-4 bg-orange-500 text-white w-12 h-12 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-orange-900 dark:text-orange-100">Rekap Masak (Dapur)</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">Lihat total porsi yang harus
                                dimasak.</p>
                        </a>

                        <a href="#"
                            class="group relative flex flex-col p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-xl hover:shadow-md transition-all">
                            <div
                                class="mb-4 bg-blue-500 text-white w-12 h-12 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 11H4v4h2v-4zM6 11V7a1 1 0 011-1h14a1 1 0 011 1v4H6z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-blue-900 dark:text-blue-100">Mode Kasir (Pickup)</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Checklist pengambilan makanan.</p>
                        </a>
                    </div>

                    <div
                        class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden mt-8">
                        <div
                            class="px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex justify-between items-center">
                            <h3 class="font-semibold">Perlu Verifikasi (Terbaru)</h3>
                            <a href="#" class="text-sm text-[#FF2D20] hover:underline">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-white/5 text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">Invoice</th>
                                        <th class="px-6 py-3 font-medium">Nama</th>
                                        <th class="px-6 py-3 font-medium">Tgl Ambil</th>
                                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                    @forelse($recentOrders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-6 py-4 font-medium">{{ $order->invoice_code }}</td>
                                            <td class="px-6 py-4">{{ $order->user->name ?? 'Guest' }}</td>
                                            <td class="px-6 py-4">
                                                {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="#" class="text-[#FF2D20] font-medium hover:underline">Cek Bukti</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                                Tidak ada pesanan yang menunggu verifikasi saat ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-white dark:bg-[#161615] p-6 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <h3 class="font-semibold mb-4 text-[#1b1b18] dark:text-white">Status Kuota Hari Ini</h3>

                        @if($menuQuotas->count() > 0)
                            @foreach($menuQuotas as $quota)
                                <div class="mb-4 last:mb-0">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="truncate pr-2">{{ $quota['name'] }}</span>
                                        <span
                                            class="font-medium {{ $quota['remaining'] < 5 ? 'text-red-500' : 'text-green-500' }}">
                                            {{ $quota['remaining'] == 0 ? 'Habis' : 'Sisa ' . $quota['remaining'] }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="{{ $quota['remaining'] < 5 ? 'bg-red-500' : 'bg-green-500' }} h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ $quota['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-right">
                                        {{ $quota['sold'] }} / {{ $quota['limit'] }} Terjual
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">Belum ada menu aktif.</p>
                        @endif

                        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <button
                                class="w-full py-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors">
                                + Kelola Menu & Limit
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>

</html>