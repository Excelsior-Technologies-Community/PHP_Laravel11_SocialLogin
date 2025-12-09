# 🎯 PHP_Laravel11_SocialLogin

This project demonstrates how to implement Google Social Login (OAuth 2.0) in a Laravel 11 application using Laravel Socialite, following real-world authentication practices.

The primary goal of this project is to showcase how modern applications allow users to authenticate securely using their Google account, eliminating the need to create and remember separate passwords. With a single click, users can sign in via Google and gain instant access to a protected dashboard.

In addition to Google OAuth authentication, the project also includes a traditional email/password login system to illustrate how social login can seamlessly coexist with standard authentication in Laravel.

Once authenticated (via Google or credentials), users are redirected to a secured dashboard, and full session management with a safe logout mechanism is implemented.

✅ Key Highlights

🔐 Google OAuth 2.0 Authentication using Laravel Socialite (Primary Focus)

⚡ One-click sign-in with Google account

🧩 Seamless integration with Laravel’s authentication system

🛡️ Secure session handling and logout

🎨 Clean, responsive UI built with Tailwind CSS

🏗️ Controller-based, clean, and scalable code structure

This project reflects how modern Laravel applications implement social authentication for better user experience, security, and faster onboarding.

## ✅ Overview

This project demonstrates **authentication in Laravel 11** using:

1️⃣ **Google OAuth (Social Login)** via Laravel Socialite  

2️⃣ **Normal Email & Password Authentication**  
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

## 🛠️ Project Setup & Configuration

---

## 🔧 Step 1: Create a New Laravel 11 Project

Run the following commands:

```bash
composer create-project laravel/laravel:^11.0 PHP_Laravel11_SocialLogin
cd PHP_Laravel11_SocialLogin
cp .env.example .env
php artisan key:generate

```
🗄 Step 2: Configure Database in .env
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

📌 Why utf8mb4?
Supports emojis

Supports Unicode

Required for Google profile names

Prevents character corruption


🗃 Step 3: Create Database
Create a database in phpMyAdmin or MySQL CLI:

```
CREATE DATABASE laravel11_social;
```

🧩 Step 4: Install Laravel Socialite
```
composer require laravel/socialite
```
✅ Laravel Socialite is Laravel’s official package for OAuth authentication.

🧱 Step 5: Update Users Table (Google ID)
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
📌 google_id stores the unique ID provided by Google OAuth.

👨‍💻 Step 6: Update User Model
📁 Location: app/Models/User.php
```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id', // Google OAuth ID
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 11 automatic hashing
        ];
    }
}

```
🔐 Step 7: Setup Google OAuth Credentials
```

Visit:

👉 https://console.cloud.google.com/

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

🧾 Step 8: Add Google Keys in .env

```

GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT=http://localhost:8000/login/google/callback

```
⚙ Step 9: Update config/services.php

```

'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT'),
],

```
🧱 Step 10: Controllers
🔐 AuthController (Normal Login & Register)
Create controller:
```

php artisan make:controller Auth/AuthController

```
📁 app/Http/Controllers/Auth/AuthController.php
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
        return view('auth.register'); // resources/views/auth/register.blade.php
    }

    /**
     * Handle user registration
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate incoming form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // password_confirmation field required
        ]);

        // Create new user
        $user = User::create([
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
        return view('auth.login'); // resources/views/auth/login.blade.php
    }

    /**
     * Handle user login
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate incoming form data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Prevent session fixation
            return redirect()->route('dashboard');
        }

        // Login failed
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
        return view('auth.dashboard'); // resources/views/auth/dashboard.blade.php
    }

    /**
     * Logout the user
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); // Invalidate session
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect()->route('login');
    }
}

```
🌐 SocialController (Google Login)
Create controller:
```
php artisan make:controller Auth/SocialController

```
📁 app/Http/Controllers/Auth/SocialController.php

```
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

```
🛣 Step 11: Routes

📁 routes/web.php
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

// ======================= Google Social Login =======================
// Redirect to Google
Route::get('login/google', [SocialController::class, 'redirectToGoogle'])->name('google.login');

// Google callback
Route::get('login/google/callback', [SocialController::class, 'handleGoogleCallback'])->name('google.callback');

```
🎨 Step 12: Views (Tailwind CSS)

📁 resources/views/auth/

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
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow w-full max-w-md">
    <!-- Page Heading -->
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <!-- Display Error Message (e.g., invalid credentials) -->
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display Success Message (e.g., after registration) -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Normal Email/Password Login Form -->
    <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf <!-- CSRF token for security -->

        <!-- Email Input -->
        <div>
            <label class="block text-gray-700 mb-1" for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Password Input -->
        <div>
            <label class="block text-gray-700 mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
            Login
        </button>
    </form>

    <!-- OR Divider -->
    <div class="flex items-center my-4">
        <hr class="flex-grow border-gray-300">
        <span class="mx-2 text-gray-500">OR</span>
        <hr class="flex-grow border-gray-300">
    </div>

    <!-- Google Login Button -->
    <a href="{{ route('google.login') }}" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded flex items-center justify-center gap-2">
         Login with Google
    </a>

    <!-- Link to Registration Page -->
    <div class="text-center mt-5">
        <p class="text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register here</a>
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
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- ============================= -->
<!-- Navbar Section -->
<!-- ============================= -->
<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <!-- Dashboard Title -->
    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>

    <!-- Logout Button -->
    <a href="{{ route('logout') }}"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition duration-200">
        Logout
    </a>
</nav>

<!-- ============================= -->
<!-- Main Content Section -->
<!-- ============================= -->
<div class="flex-grow flex items-center justify-center">
    <!-- Card Container -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-lg p-8 text-center">
        <!-- Welcome Message -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            Welcome, {{ Auth::user()->name }}!
        </h2>

        <!-- Display login method -->
        <p class="text-gray-600 mb-6">
            You are successfully logged in using 
            {{ Auth::user()->google_id ? 'Google' : 'normal credentials' }}.
        </p>
    </div>
</div>

<!-- ============================= -->
<!-- Footer Section -->
<!-- ============================= -->
<footer class="bg-white shadow py-4 text-center text-gray-500">
    &copy; {{ date('Y') }} Your Company. All rights reserved.
</footer>

</body>
</html>

```
✅ Clean UI
✅ Validation messages
✅ Google Login button


🧪 Step 13: Run the Project
```
php artisan serve
```
Open in browser:
```

http://localhost:8000/login
```
🧑‍💻 Application Code Structure
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
🎉 Project Status
✅ Fully Functional
✅ Laravel 11 Ready
✅ Google OAuth Integrated

🎉 Your Laravel 11 Social Login with Google Project is Successfully Ready!
