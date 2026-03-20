<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ── Public Routes (no auth needed) ──────────────────

Route::get('/', fn() => redirect()->route('login'));

// Registration — only works when 0 users exist
Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Login / Logout
Route::get('/login',   [LoginController::class, 'showForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot password placeholder (we'll build this later)
Route::get('/forgot-password', fn() => view('auth.login'))->name('password.request');

// ── Protected Routes (auth required) ────────────────
Route::middleware(['auth'])->group(function () {

    // Super Admin + Team Member dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Customer dashboard (separate view)
    Route::get('/customer/dashboard', fn() => view('dashboard'))->name('customer.dashboard');

});