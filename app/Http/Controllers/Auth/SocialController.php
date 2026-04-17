<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Redirect to Google's authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google after authentication.
     */
    public function handleGoogleCallback()
    {
        return $this->handleSocialLogin('google');
    }

    /**
     * Redirect to Twitter's authentication page.
     */
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Handle callback from Twitter after authentication.
     */
    public function handleTwitterCallback()
    {
        return $this->handleSocialLogin('twitter');
    }

    /**
     * Common function to handle Social Login for different providers.
     */
    protected function handleSocialLogin($provider)
    {
        try {
            // 🛠️ FIX 1: Twitter (OAuth 1.0) does NOT support stateless()
            if ($provider === 'twitter') {
                $socialUser = Socialite::driver($provider)->user();
            } else {
                $socialUser = Socialite::driver($provider)->stateless()->user();
            }

            $socialId = $socialUser->getId();
            $socialEmail = $socialUser->getEmail();

            // Check if user already exists in our database (by provider ID or Email)
            $user = User::where($provider . '_id', $socialId);
            
            if ($socialEmail) {
                $user->orWhere('email', $socialEmail);
            }
            
            $user = $user->first();

            if (!$user) {
               
                $finalEmail = $socialEmail ?? $socialId . '@' . $provider . '.com';

                // Register a new user if not found
                $user = User::create([
                    'name'            => $socialUser->getName() ?? 'User_' . $socialId,
                    'email'           => $finalEmail, 
                    'avatar'          => $socialUser->getAvatar(), 
                    $provider . '_id' => $socialId,
                    'password'        => bcrypt(Str::random(16)),
                    'provider'        => $provider,
                    'last_login_at'   => now(),
                ]);
            } else {
                // Update existing user info
                $user->update([
                    'avatar'          => $socialUser->getAvatar(), 
                    $provider . '_id' => $socialId,
                    'provider'        => $provider,
                    'last_login_at'   => now(),
                ]);
            }

            // Log the user into the application
            Auth::login($user, true);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            // Redirect back to login with actual error message for debugging
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login failed: ' . $e->getMessage());
        }
    }
}