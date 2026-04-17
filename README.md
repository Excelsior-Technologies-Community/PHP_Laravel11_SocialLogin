#  PHP_Laravel11_SocialLogin

- This project demonstrates how to implement Google Social Login (OAuth 2.0) in a Laravel 11 application using Laravel Socialite, following real-world authentication practices.

- The primary goal of this project is to showcase how modern applications allow users to authenticate securely using their Google account, eliminating the need to create and remember separate passwords. With a single click, users can sign in via Google and gain instant access to a protected dashboard.

- In addition to Google OAuth authentication, the project also includes a traditional email/password login system to illustrate how social login can seamlessly coexist with standard authentication in Laravel.

- Once authenticated (via Google or credentials), users are redirected to a secured dashboard, and full session management with a safe logout mechanism is implemented.

# Key Highlights

 - Google OAuth 2.0 Authentication using Laravel Socialite (Primary Focus)

 - One-click sign-in with Google account

 - Seamless integration with Laravel’s authentication system

 - Secure session handling and logout

 - Clean, responsive UI built with Tailwind CSS

 - Controller-based, clean, and scalable code structure

- This project reflects how modern Laravel applications implement social authentication for better user experience, security, and faster onboarding.

##  Overview

This project demonstrates **authentication in Laravel 11** using:

1️ **Google OAuth (Social Login)** via Laravel Socialite  

2️ **Normal Email & Password Authentication**  
   - Register  
   - Login  
   - Dashboard  
   - Logout  

This project is suitable for:
- College / University submission  
- Interview demonstration  
- GitHub portfolio  
- Real-world authentication learning  

---

##  Project Setup & Configuration

---

##  Step 1: Create a New Laravel 11 Project

Run the following commands:

```bash
composer create-project laravel/laravel:^11.0 PHP_Laravel11_SocialLogin
cd PHP_Laravel11_SocialLogin
cp .env.example .env
php artisan key:generate

```
 Step 2: Configure Database in .env
 
Update your database configuration:
env
```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel11_social
DB_USERNAME=root
DB_PASSWORD=

```

# Charset & Collation (important for emojis, unicode, Google names)

```
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

Why utf8mb4?
Supports emojis

Supports Unicode

Required for Google profile names

Prevents character corruption


 Step 3: Create Database
 
 Create a database in phpMyAdmin or MySQL CLI:

```
CREATE DATABASE laravel11_social;
```

 Step 4: Install Laravel Socialite
```
composer require laravel/socialite
composer require doctrine/dbal
```
 Laravel Socialite is Laravel’s official package for OAuth authentication.

 Step 5: Update Users Table (Google ID)
If users table already exists, add google_id:
```
php artisan make:migration add_google_id_to_users_table --table=users

```
Migration Code
```
php
Copy code
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email'); 
            // google_id will store the unique ID from Google
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
        });
    }
};

```
Run migration:
```
php artisan migrate

```
 google_id stores the unique ID provided by Google OAuth.

 Step 6: Update User Model
 
 Location: app/Models/User.php
```

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'twitter_id',  
        'avatar',
        'last_login_at',
        'provider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}


```
 Step 7: Setup Google OAuth Credentials
```

Visit:

 https://console.cloud.google.com/

```
Steps:

APIs & Services → Credentials

Create Credentials → OAuth Client ID

Application Type → Web Application

Add Redirect URL:
```
http://localhost:8000/login/google/callback
```

Client ID

Client Secret

 Step 8: Add Google Keys in .env

```

GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT=http://localhost:8000/login/google/callback

Twitter keys

TWITTER_CLIENT_ID=your_client_id_here
TWITTER_CLIENT_SECRET=your_client_secret_here
TWITTER_REDIRECT_URI="http://localhost:8000/auth/twitter/callback"

```
 Step 9: Update config/services.php

```

'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT'),
],

'twitter' => [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'redirect' => env('TWITTER_REDIRECT_URL'),
],

```
 Step 10: Controllers
 
 AuthController (Normal Login & Register)
 
Create controller:
```

php artisan make:controller Auth/AuthController

```
 app/Http/Controllers/Auth/AuthController.php
```

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the registration form
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate incoming form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', 
        ]);

        // Create new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Registration successful! You can now login.');
    }

    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ✅ Update Login Info
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'provider' => 'normal'
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Show user dashboard (protected route)
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('auth.dashboard');
    }

    /**
     * Logout the user
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect()->route('login');
    }
}

```
 SocialController (Google Login)
 
Create controller:
```
php artisan make:controller Auth/SocialController

```
 app/Http/Controllers/Auth/SocialController.php

```
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

```
 Step 11: Routes

 routes/web.php
```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// ======================= Normal Auth =======================
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

// Dashboard (protected)
Route::get('dashboard', [AuthController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

// Logout
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// ======================= Social Login (Google & Twitter) =======================

// --- Google Social Login ---
// Redirect to Google
Route::get('login/google', [SocialController::class, 'redirectToGoogle'])->name('google.login');
// Google callback
Route::get('auth/callback/google', [SocialController::class, 'handleGoogleCallback'])->name('google.callback');

// --- Twitter Social Login ---
// Redirect to Twitter
Route::get('login/twitter', [SocialController::class, 'redirectToTwitter'])->name('twitter.login');
// Twitter callback
Route::get('auth/twitter/callback', [SocialController::class, 'handleTwitterCallback'])->name('twitter.callback');

```
 Step 12: Views (Tailwind CSS)

 resources/views/auth/

register.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Registration Form -->
    <form action="{{ route('register') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-gray-700 mb-1" for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="Enter your name"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-gray-700 mb-1" for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Password -->
        <div>
            <label class="block text-gray-700 mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-gray-700 mb-1" for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
            Register
        </button>
    </form>

    <!-- Login Link -->
    <div class="text-center mt-5">
        <p class="text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login here</a>
        </p>
    </div>
</div>

</body>
</html>

```
login.blade.php
```


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border-t-4 border-blue-600">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login Now</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="name@example.com"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-200">
            Login
        </button>
    </form>

    <div class="flex items-center my-6">
        <hr class="flex-grow border-gray-200">
        <span class="mx-3 text-gray-400 text-sm font-medium">OR LOGIN WITH</span>
        <hr class="flex-grow border-gray-200">
    </div>

    <div class="flex flex-col gap-3">
        
        <a href="{{ route('google.login') }}" 
           class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 rounded-lg flex items-center justify-center gap-2 font-medium shadow-sm transition">
            <i class="bi bi-google text-red-500"></i> Continue with Google
        </a>

        <a href="{{ route('twitter.login') }}" 
           class="w-full bg-sky-500 hover:bg-sky-600 text-white py-2 rounded-lg flex items-center justify-center gap-2 font-medium shadow-sm transition">
            <i class="bi bi-twitter"></i> Continue with Twitter
        </a>

    </div>

    <div class="text-center mt-8 pt-4 border-t border-gray-100">
        <p class="text-gray-600 text-sm">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Register here</a>
        </p>
    </div>
</div>

</body>
</html>

```
dashboard.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    
    <h1 class="text-xl font-bold text-gray-800">
        Dashboard
    </h1>

    <div class="flex items-center gap-4">
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="Profile" class="w-8 h-8 rounded-full shadow-sm border border-gray-200">
        @endif
        
        <a href="{{ route('logout') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">
            Logout
        </a>
    </div>

</nav>

<div class="flex-grow flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl w-full max-w-lg p-8 text-center border-t-4 border-blue-500">

        <div class="mb-6 flex justify-center">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="User Photo" 
                     class="w-24 h-24 rounded-full border-4 border-blue-100 shadow-md object-cover">
            @else
                <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-md">
                    <i class="bi bi-person-fill text-5xl"></i>
                </div>
            @endif
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-2">
            Welcome, {{ Auth::user()->name }} 👋
        </h2>

        <p class="text-gray-500 mb-4">
            {{ Auth::user()->email }}
        </p>

        <hr class="my-4 border-gray-100">

        <p class="text-gray-700 mb-2">
            Login Method:
            <span class="font-semibold text-blue-600 capitalize">
                {{ Auth::user()->provider ?? 'normal' }}
            </span>
        </p>

        <p class="text-gray-700 mb-4">
            Last Login:
            <span class="font-semibold text-green-600">
                {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('d M Y, h:i A') : 'First Login' }}
            </span>
        </p>

        <div class="mt-4 flex justify-center gap-2">
            @if(Auth::user()->google_id)
                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-google me-1"></i> Google User
                </span>
            @elseif(Auth::user()->twitter_id)
                <span class="inline-block bg-sky-100 text-sky-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-twitter me-1"></i> Twitter User
                </span>
            @else
                <span class="inline-block bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-person me-1"></i> Normal User
                </span>
            @endif
        </div>

    </div>

</div>

<footer class="bg-white shadow py-4 text-center text-gray-500 mt-auto">
    © {{ date('Y') }} Laravel Social Login. All rights reserved.
</footer>

</body>
</html>

```
 Clean UI
 Validation messages
 Google Login button


Step 13: Run the Project
```
php artisan serve
```
Open in browser:
```

http://localhost:8000/login
```
 Application Code Structure
```
PHP_Laravel11_SocialLogin/
app/
 └── Http/
     └── Controllers/
         └── Auth/
             ├── AuthController.php
             └── SocialController.php

database/
 └── migrations/

resources/
 └── views/
     └── auth/
         ├── login.blade.php
         ├── register.blade.php
         └── dashboard.blade.php

routes/
 └── web.php

.env
```
## Output:

### User Registration

<img width="1919" height="1088" alt="Screenshot 2025-12-12 123246" src="https://github.com/user-attachments/assets/7d6bbf2e-2bf3-4623-a703-1bb6a2c49dee" />

### User Login

<img width="1911" height="1086" alt="Screenshot 2025-12-04 111648" src="https://github.com/user-attachments/assets/3d0569df-7f23-49ea-adf1-2a9e08eead22" />

### Sign in With Google

<img width="1918" height="1034" alt="Screenshot 2025-12-04 111840" src="https://github.com/user-attachments/assets/ddf4affe-dbbe-4d7c-9d7e-264d28e31651" />

### User Dashboard

<img width="1916" height="1083" alt="Screenshot 2025-12-04 111854" src="https://github.com/user-attachments/assets/1f498c47-79a6-47f7-b5cf-8c8d9884b933" />

---

Your PHP_Laravel11_SocialLogin Project is Successfully Ready!
