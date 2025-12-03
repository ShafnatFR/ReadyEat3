<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Tampilkan Form Login
    public function showLoginForm()
    {
        // Jika sudah login sebagai admin, lempar langsung ke dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login (Auth::attempt)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // 3. Cek Role Admin
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Jika bukan admin, logout paksa & beri pesan error
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda tidak memiliki akses admin.',
            ])->onlyInput('email');
        }

        // 4. Jika password salah
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}