<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PartnerRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.partner_register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'partner_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partners', 's3');
            $logoPath = Storage::disk('s3')->url($logoPath);
        }

        $partner = Partner::create([
            'name' => $request->partner_name,
            'logo_url' => $logoPath,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'partner',
            'partner_id' => $partner->id,
        ]);

        auth()->login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Registrasi Organizer berhasil! Selamat datang di dashboard.');
    }
}
