<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAccountController extends Controller
{
    /**
     * Show user account page
     */
    public function index(Request $request)
    {
        // Get active tab from query param, default to 'profile'
        $activeTab = $request->query('tab', 'profile');

        // Validate tab value
        if (!in_array($activeTab, ['profile', 'orders'])) {
            $activeTab = 'profile';
        }

        // Get user with orders (eager loading for performance)
        $user = Auth::user()->load(['orders.items.menu']);

        // Sort orders by pickup date descending
        $orders = $user->orders->sortByDesc('pickup_date');

        return view('account', [
            'activeTab' => $activeTab,
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * Update user profile (name and phone)
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9]+$/',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak benar.',
            ]);
        }

        // Check if new password is same as current
        if (Hash::check($validated['new_password'], $user->password)) {
            throw ValidationException::withMessages([
                'new_password' => 'Password baru tidak boleh sama dengan password saat ini.',
            ]);
        }

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Logout from all other devices (security best practice)
        Auth::logoutOtherDevices($validated['new_password']);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
