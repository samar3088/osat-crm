<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login submission
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting — max 5 attempts per minute per IP+email
        $key = Str::lower($request->email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Attempt login
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Prevent session fixation attacks
            RateLimiter::clear($key);          // Clear rate limit on success

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact your administrator.',
                ]);
            }

            // ── Role-based redirect ──────────────────
            return match(true) {
                $user->isSuperAdmin() => redirect()->route('dashboard')->with('success', "Welcome back, {$user->name}!"),
                $user->isTeamMember() => redirect()->route('dashboard')->with('success', "Welcome back, {$user->name}!"),
                $user->isCustomer()   => redirect()->route('customer.dashboard')->with('success', "Welcome back, {$user->name}!"),
                default               => redirect()->route('dashboard'),
            };
        }

        // Failed login
        RateLimiter::hit($key);

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}