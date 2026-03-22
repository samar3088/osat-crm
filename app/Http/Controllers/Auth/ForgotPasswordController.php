<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link via AJAX
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent! Please check your email.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'We could not find a user with that email address.',
        ], 422);
    }
}