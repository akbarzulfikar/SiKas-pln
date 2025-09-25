<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Exception;

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

        try {
            // Test database connection first
            DB::connection()->getPdo();
            
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
                // Regenerate session untuk security
                $request->session()->regenerate();
                $request->session()->migrate(true);
                
                // Update last login
                $user->update(['last_login' => now()]);
                
                // Pastikan session tersimpan
                $request->session()->save();
                
                Log::info('User login successful', ['username' => $validated['username'], 'user_id' => Auth::user()->user_id]);
                
                return redirect()->intended(route('dashboard.index'))
                    ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
            }

            return back()->withErrors(['username' => 'Password salah.'])->onlyInput('username');
            
        } catch (Exception $e) {
            Log::error('Database connection failed during login', [
                'error' => $e->getMessage(),
                'username' => $validated['username']
            ]);
            
            return back()->withErrors([
                'username' => 'Terjadi masalah koneksi ke database. Silakan coba lagi nanti atau hubungi administrator.'
            ])->onlyInput('username');
        }
    }

    /**
     * Handle logout - PERBAIKAN CSRF
     */
    public function logout(Request $request)
    {
        try {
            // ✅ PERBAIKAN: Skip CSRF validation karena session mungkin sudah expired
            // Laravel akan handle ini secara otomatis jika middleware benar
            
            $username = Auth::user()->username ?? 'Unknown';

            Auth::logout();
            
            // ✅ PERBAIKAN: Order yang benar untuk session handling
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flush();

            Log::info('User logged out', ['username' => $username]);

            return redirect()->route('login')->with('success', 'Berhasil logout');
            
        } catch (Exception $e) {
            Log::error('Error during logout', ['error' => $e->getMessage()]);
            
            // Force logout anyway
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('warning', 'Logout berhasil, namun terjadi masalah teknis.');
        }
    }
}