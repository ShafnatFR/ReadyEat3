<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth' }} - ReadyEat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 right-0 h-48 w-48 bg-orange-500 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-48 w-48 bg-amber-400 rounded-full opacity-20 blur-3xl"></div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md z-10">
        <a href="{{ url('/') }}" class="flex items-center justify-center space-x-2">
            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20l-5-5-5 5m0-16l5 5 5-5"></path>
                </svg>
            </div>
            <span class="text-3xl font-bold text-gray-800">ReadyEat</span>
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            {{ $title }}
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10">
        <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10">
            {{ $slot }}
        </div>
    </div>
</body>

</html>