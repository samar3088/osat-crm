<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     * BUT only if no users exist yet (first user = Super Admin)
     */
    public function showForm(): View|RedirectResponse
    {
        // If users already exist, block registration
        // Team members & customers are created by Super Admin only
        if (User::count() > 0) {
            return redirect()->route('login')
                ->with('error', 'Registration is closed. Please contact your administrator.');
        }

        return view('auth.register');
    }

    /**
     * Handle registration form submission
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // Double-check — block if users already exist
        if (User::count() > 0) {
            return redirect()->route('login')
                ->with('error', 'Registration is closed. Please contact your administrator.');
        }

        // Create the Super Admin user
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'employee_code' => $request->employee_code,
            'password'      => $request->password, // Auto-hashed by model cast
            'is_active'     => true,
        ]);

        // Assign Super Admin role via Spatie
        $user->assignRole('super_admin');

        // Log them in immediately
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome! Your Super Admin account has been created.');
    }
}