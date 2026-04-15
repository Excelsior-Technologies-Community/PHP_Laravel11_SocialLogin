<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite; // Google OAuth
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        // Redirect to Google's OAuth 2.0 authentication page
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google after authentication.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            // New user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'provider' => 'google',              // ✅ NEW
                'last_login_at' => now(),            // ✅ NEW
            ]);
        } else {
            // Existing user
            $user->update([
                'google_id' => $googleUser->getId(),
                'provider' => 'google',              // ✅ NEW
                'last_login_at' => now(),            // ✅ NEW
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');

    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Google login failed.');
    }
}
}
