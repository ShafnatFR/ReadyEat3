<x-auth-layout title="Reset your password">
    <p class="text-center text-sm text-gray-600 mb-4">
        Enter your email address and we will send you a link to reset your password.
    </p>

    <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
        @csrf

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <p class="text-sm">{{ session('status') }}</p>
            </div>
        @endif

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
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                Send reset link
            </button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Remember your password?
        <a href="{{ route('login') }}" class="font-medium text-orange-500 hover:text-orange-700">Back to sign in</a>
    </p>
</x-auth-layout>