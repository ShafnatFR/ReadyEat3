<x-auth-layout title="Admin Login">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div
                class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                Address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </div>

        <div>
            <label for="password"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Remember me
                </label>
            </div>
        </div>

        <div>
            <button type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                Sign in as Admin
            </button>
        </div>

        <div class="text-center">
            <a href="{{ url('/') }}" class="text-sm font-medium text-primary hover:text-orange-600">
                ← Back to Site
            </a>
        </div>
    </form>
</x-auth-layout>