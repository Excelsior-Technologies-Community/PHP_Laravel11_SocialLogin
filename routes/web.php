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