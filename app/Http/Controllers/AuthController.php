<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Check if user exists and is active
        $user = User::where('username', $validated['username'])->first();
        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->onlyInput('username');
        }
        if (!$user->is_active) {
            return back()->withErrors(['username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'])->onlyInput('username');
        }

        // Attempt authentication
        if (Auth::attempt(['username' => $validated['username'], 'password' => $validated['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user->update(['last_login' => now()]);
            
            return redirect()->intended(route('dashboard.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['username' => 'Password salah.'])->onlyInput('username');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $username = Auth::user()->username ?? 'Unknown';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['username' => $username]);

        return redirect()->route('login')->with('success', 'Berhasil logout');
    }
}