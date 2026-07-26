<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleSSOController extends Controller
{
    public function redirect(Request $request)
    {
        $driver = Socialite::driver('google')->stateless();
        
        $stateParams = [];
        if ($request->has('event_id')) {
            $stateParams[] = 'event_id=' . $request->event_id;
        }
        if ($request->has('tier_id')) {
            $stateParams[] = 'tier_id=' . $request->tier_id;
        }
        if (!empty($stateParams)) {
            $driver->with(['state' => implode('&', $stateParams)]);
        }
        
        return $driver->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => bcrypt(Str::random(16)),
            ]
        );

        Auth::login($user);

        // Ambil event_id dan tier_id dari state parameter (Bypass isu Session Cookie yang hilang)
        $eventId = null;
        $tierId = null;
        if ($request->has('state')) {
            parse_str($request->state, $stateData);
            $eventId = $stateData['event_id'] ?? null;
            $tierId = $stateData['tier_id'] ?? null;
        }

        if ($eventId) {
            $redirectParams = ['event' => $eventId];
            if ($tierId) {
                $redirectParams['tier_id'] = $tierId;
            }
            return redirect()->route('checkout.create', $redirectParams)->with('success', 'Berhasil masuk dengan akun Google! Silakan periksa kembali rincian tiket dan lengkapi nomor WhatsApp Anda.');
        }

        if (in_array($user->role, ['superadmin', 'partner'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }
}

