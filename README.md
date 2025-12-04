# 🎯 Project: Laravel11-social-google

## ✅ Overview
This project demonstrates how to implement authentication in Laravel 11 using:

1. **Google OAuth Login (Social Login)** via Laravel Socialite  
2. **Normal Email/Password Login** (Register, Login, Dashboard, Logout)  

It allows users to login/register using either traditional credentials or Google account.

---

## 🛠️ Project Setup & Configuration

### 🔧 Step 1: Create a New Laravel 11 Project
```bash
composer create-project laravel/laravel:^11.0 laravel11-social-google
cd laravel11-social-google
cp .env.example .env
php artisan key:generate
🗄 Step 2: Configure Database
Open .env and update:

makefile
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel11_social
DB_USERNAME=root
DB_PASSWORD=

# Charset & Collation for emojis / unicode
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
Then create the database laravel11_social in MySQL.

🧩 Step 3: Install Laravel Socialite

composer require laravel/socialite
🧱 Step 4: Update Users Table
Add google_id column for Google OAuth:


php artisan make:migration add_google_id_to_users_table --table=users
php artisan migrate
👨‍💻 Step 5: Update User Model
Add google_id to $fillable

Add $hidden fields

Ensure password hashing (hashed)

Location: app/Models/User.php

🔐 Step 6: Setup Google OAuth Credentials
Go to Google Cloud Console

APIs & Services → Credentials → Create Credentials → OAuth Client ID

Application Type: Web Application

Add redirect URL:
http://localhost:8000/login/google/callback
Copy Client ID and Client Secret.

🧾 Step 7: Add Google Keys in .env
ini

GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_secret_here
GOOGLE_REDIRECT=http://localhost:8000/login/google/callback
Update config/services.php:

php
Copy code
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
],

🧱 Step 8: Create Controllers
AuthController – handles email/password login & registration

php artisan make:controller Auth/AuthController
SocialController – handles Google OAuth login

php artisan make:controller Auth/SocialController

🛣 Step 9: Define Routes
File: routes/web.php

Normal Auth: /register, /login, /dashboard, /logout

Google OAuth: /login/google, /login/google/callback

🎨 Step 10: Create Views
Folder: resources/views/auth/

register.blade.php – Registration form

login.blade.php – Login form with Google login button

dashboard.blade.php – User Dashboard

(Use Tailwind CSS for modern, responsive UI.)

🧪 Step 11: Run the Project

php artisan serve
Open in browser: http://localhost:8000

/login → Login page

/register → Registration page

/dashboard → Dashboard (after login)

/login/google → Redirect to Google login

🧑‍💻 Application Code Structure
app/
  └── Http/
       └── Controllers/
            └── Auth/
                 ├── AuthController.php       # Email/Password Login/Register
                 └── SocialController.php     # Google OAuth Login
database/
  └── migrations/                        # users table & google_id migration
resources/
  └── views/
       └── auth/
           ├── login.blade.php
           ├── register.blade.php
           └── dashboard.blade.php
routes/
  └── web.php
.env

🎉 Project Complete
You now have a Laravel 11 project supporting both normal email/password authentication and Google social login.
Users can register/login via standard credentials or Google, and access a protected dashboard.
