<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserTarget;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;

class TeamMemberService
{
    /**
     * Get all team members — scoped by role
     * DRY: reused in controller and AJAX
     */
    public function getAll(string $search = ''): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::role('team_member')
            ->with(['assignedTo', 'targets'])
            ->withCount('clients');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    /**
     * Create a new team member
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'employee_code' => $data['employee_code'] ?? null,
            'work_type'     => $data['work_type'] ?? 'Sales',
            'password'      => $data['password'],
            'is_active'     => true,
            'assigned_to'   => $data['assigned_to'] ?? null,
        ]);

        // Assign role
        $user->assignRole('team_member');

        // Log action
        AuditLog::record(
            'created_team_member',
            'team_members',
            "Created team member: {$user->name}"
        );

        return $user;
    }

    /**
     * Update existing team member
     */
    public function update(User $user, array $data): User
    {
        $updateData = [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'employee_code' => $data['employee_code'] ?? null,
            'work_type'     => $data['work_type'] ?? 'Sales',
            'assigned_to'   => $data['assigned_to'] ?? null,
            'is_active'     => $data['is_active'] ?? $user->is_active,
        ];

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        AuditLog::record(
            'updated_team_member',
            'team_members',
            "Updated team member: {$user->name}"
        );

        return $user->fresh();
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);

        AuditLog::record(
            'toggled_team_member_status',
            'team_members',
            "Changed status of {$user->name} to " . ($user->is_active ? 'Active' : 'Inactive')
        );

        return $user->fresh();
    }

    /**
     * Soft delete team member
     */
    public function delete(User $user): void
    {
        AuditLog::record(
            'deleted_team_member',
            'team_members',
            "Deleted team member: {$user->name}"
        );

        $user->delete();
    }

    /**
     * Set target for team member
     */
    public function setTarget(User $user, array $data): UserTarget
    {
        return UserTarget::updateOrCreate(
            [
                'user_id' => $user->id,
                'year'    => $data['year'],
                'month'   => $data['month'],
                'type'    => $data['type'],
            ],
            [
                'plan'                      => $data['plan'] ?? null,
                'category'                  => $data['category'] ?? null,
                'target_amount'             => $data['target_amount'],
                'target_investors'          => $data['target_investors'] ?? 0,
                'target_amount_achieved'    => 0,
                'target_investors_achieved' => 0,
            ]
        );
    }

    /**
     * Get all admins for assign dropdown
     */
    public function getAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role('super_admin')->get(['id', 'name']);
    }
}