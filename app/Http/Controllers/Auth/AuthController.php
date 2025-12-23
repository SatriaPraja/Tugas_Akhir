<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login'); // Nama file view login kita
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        // 1. Proses logout user
        Auth::logout();

        // 2. Menghapus data session agar tidak bisa diakses kembali via tombol 'back' browser
        $request->session()->invalidate();

        // 3. Menghasilkan token baru untuk keamanan (mencegah session fixation)
        $request->session()->regenerateToken();

        // 4. ARAHKAN KE HALAMAN LOGIN
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
