<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - {{ config('app.name', 'ReadyEat') }}</title>

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
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] font-sans antialiased min-h-screen flex flex-col items-center justify-center p-6">

    <div class="w-full max-w-sm bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl shadow-sm p-8">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-[#FF2D20]/10 text-[#FF2D20] mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold">Admin Login</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Masuk untuk mengelola katering.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium mb-1.5 text-gray-700 dark:text-gray-300">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0a0a0a] focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition-all outline-none"
                    placeholder="admin@readyeat.com">
                @error('email')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1.5 text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0a0a0a] focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition-all outline-none"
                    placeholder="••••••••">
                @error('password')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#FF2D20] focus:ring-[#FF2D20]">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-[#FF2D20] hover:bg-[#d92215] text-white font-medium rounded-lg transition-colors focus:ring-4 focus:ring-[#FF2D20]/20">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-gray-500 hover:text-[#FF2D20] transition-colors">
                ← Kembali ke Halaman Depan
            </a>
        </div>
    </div>

</body>
</html>