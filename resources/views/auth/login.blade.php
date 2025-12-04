<x-auth-layout title="Sign in to your account">
    <form class="space-y-6" method="POST" action="{{ route('login') }}">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                @foreach($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required
                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500" />
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded" />
                <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <div class="text-sm">
                <a href="{{ route('password.request') }}"
                    class="font-medium text-orange-500 hover:text-orange-700">Forgot password?</a>
            </div>
        </div>

        <div>
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                Sign in
            </button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Not a member?
        <a href="{{ route('register') }}" class="font-medium text-orange-500 hover:text-orange-700">Create account</a>
    </p>
</x-auth-layout>