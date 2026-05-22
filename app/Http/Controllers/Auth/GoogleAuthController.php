<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google after user authorization.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        // Find existing user by google_id first, then by email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Reject CMS staff from using OAuth
            if ($user->hasAnyRole(['superadmin', 'author', 'admin-marketplace'])) {
                return redirect()->route('login')
                    ->with('error', 'Akun staf CMS tidak dapat login menggunakan Google OAuth. Gunakan login manual.');
            }

            // Link google_id if not yet set (email match case)
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            // Create new user from Google profile
            $user = User::create([
                'name'              => $googleUser->getName(),
                'username'          => $this->generateUsername($googleUser->getEmail()),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                // OAuth users have no real password — store a random unhashed placeholder
                // so the NOT NULL constraint is satisfied and manual login is impossible
                'password'          => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }

    /**
     * Generate a unique username from an email address.
     * e.g. "john.doe@gmail.com" → "johndoe" or "johndoe_2" if taken.
     */
    private function generateUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '');
        $base = preg_replace('/[^a-z0-9]/', '', strtolower($base));
        $base = $base ?: 'user';

        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        return $username;
    }
}
