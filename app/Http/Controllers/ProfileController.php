<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Client;
use App\Models\UserTarget;

class ProfileController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $stats = $this->getUserStats($user);
        return view('profile.index', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'min:3', 'max:100'],
            'employee_code' => ['nullable', 'string', 'max:50'],
            'work_type'     => ['nullable', 'string', 'max:50'],
        ]);

        auth()->user()->update([
            'name'          => $request->name,
            'employee_code' => $request->employee_code,
            'work_type'     => $request->work_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
            ],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        auth()->user()->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    private function getUserStats($user): array
    {
        if ($user->isSuperAdmin()) {
            return [
                'total_users'   => \App\Models\User::count(),
                'total_clients' => Client::count(),
                'total_team'    => \App\Models\User::role('team_member')->count(),
            ];
        }

        if ($user->isTeamMember()) {
            $target = UserTarget::where('user_id', $user->id)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->first();

            return [
                'total_clients'   => Client::where('assigned_to', $user->id)->count(),
                'target_amount'   => $target?->target_amount ?? 0,
                'target_achieved' => $target?->target_amount_achieved ?? 0,
                'target_pct'      => $target && $target->target_amount > 0
                    ? round(($target->target_amount_achieved / $target->target_amount) * 100)
                    : 0,
            ];
        }

        return [];
    }
}