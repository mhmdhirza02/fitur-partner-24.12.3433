<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (in_array(auth()->user()->role, ['superadmin', 'partner'])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (in_array(auth()->user()->role, ['superadmin', 'partner'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan tidak valid atau belum terdaftar.',
        ])->withInput($request->only('email'));
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            if (in_array(auth()->user()->role, ['superadmin', 'partner'])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('auth.user_register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau langsung login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat! Selamat datang di AmikomEventHub.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
