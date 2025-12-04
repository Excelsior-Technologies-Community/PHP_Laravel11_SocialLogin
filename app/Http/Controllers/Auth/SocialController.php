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
            // Retrieve user information from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if user exists in DB either by google_id or email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if (!$user) {
                // User does not exist: create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)), // Random password for Google users
                ]);
            } elseif (!$user->google_id) {
                // User exists but google_id is empty: update google_id
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // Log in the user using Laravel Auth
            Auth::login($user, true);

            // Redirect to dashboard
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            // If any error occurs, redirect back to login with error message
            return redirect()->route('login')->with('error', 'Google login failed.');
        }
    }
}
