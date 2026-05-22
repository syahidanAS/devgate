<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Generate 2FA Secret and show QR Code to enable 2FA.
     */
    public function setup()
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        if ($user->google2fa_secret) {
            return redirect()->route('profile.edit')->with('warning', '2FA sudah aktif.');
        }

        $secret = $google2fa->generateSecretKey();
        
        // Save to session temporarily until verified
        session(['2fa_secret_temp' => $secret]);

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('profile.partials.setup-2fa', compact('QR_Image', 'secret'));
    }

    /**
     * Verify OTP and enable 2FA permanently.
     */
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();
        $secret = session('2fa_secret_temp');

        if (!$secret) {
            return redirect()->route('profile.edit')->with('error', 'Sesi 2FA habis, silakan ulangi.');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->otp);

        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->save();
            session()->forget('2fa_secret_temp');

            return redirect()->route('profile.edit')->with('success', 'Two Factor Authentication berhasil diaktifkan.');
        }

        return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $user = auth()->user();
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Two Factor Authentication berhasil dinonaktifkan.');
    }
}

