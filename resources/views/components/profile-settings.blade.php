@props(['user'])

<div class="space-y-8">
    {{-- Profile Information Form --}}
    <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PATCH')

        <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Personal Information</h3>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary @error('phone') border-red-500 @enderror">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address (cannot be changed)</label>
            <input type="email" id="email" value="{{ $user->email }}" disabled
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 text-gray-500 cursor-not-allowed">
        </div>

        <div class="text-right">
            <button type="submit"
                class="bg-primary text-white font-semibold px-6 py-2 rounded-md hover:bg-orange-600 transition-colors shadow-sm hover:shadow-md">
                Save Changes
            </button>
        </div>
    </form>

    {{-- Change Password Form --}}
    <form action="{{ route('account.password.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PATCH')

        <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Change Password</h3>

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <input type="password" name="current_password" id="current_password" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary @error('current_password') border-red-500 @enderror">
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input type="password" name="new_password" id="new_password" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary @error('new_password') border-red-500 @enderror">
            @error('new_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
        </div>

        <div>
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New
                Password</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
        </div>

        <div class="text-right">
            <button type="submit"
                class="bg-gray-800 text-white font-semibold px-6 py-2 rounded-md hover:bg-gray-700 transition-colors shadow-sm hover:shadow-md">
                Change Password
            </button>
        </div>
    </form>
</div>