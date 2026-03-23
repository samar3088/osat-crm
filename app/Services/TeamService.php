<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\AuditLog;

class TeamService
{
    /**
     * Get all teams for DataTables
     */
    public function getAll(): \Illuminate\Database\Eloquent\Builder
    {
        return Team::withCount(['members', 'clients'])
            ->with('createdBy:id,name')
            ->latest();
    }

    /**
     * Create a new team
     */
    public function create(array $data): Team
    {
        $team = Team::create([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?? null,
            'is_active'   => true,
            'created_by'  => auth()->id(),
        ]);

        AuditLog::record(
            'created_team',
            'teams',
            "Created team: {$team->name} ({$team->code})"
        );

        return $team;
    }

    /**
     * Update team
     */
    public function update(Team $team, array $data): Team
    {
        $team->update([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?? null,
        ]);

        AuditLog::record(
            'updated_team',
            'teams',
            "Updated team: {$team->name} ({$team->code})"
        );

        return $team->fresh();
    }

    /**
     * Toggle team active status
     */
    public function toggleStatus(Team $team): Team
    {
        $team->update(['is_active' => !$team->is_active]);

        AuditLog::record(
            'toggled_team_status',
            'teams',
            "Changed team {$team->name} status to " . ($team->is_active ? 'Active' : 'Inactive')
        );

        return $team->fresh();
    }

    /**
     * Delete team — unassign all members and clients first
     */
    public function delete(Team $team): void
    {
        // Unassign all members from team
        User::where('team_id', $team->id)
            ->update(['team_id' => null]);

        // Unassign all clients from team
        \App\Models\Client::where('team_id', $team->id)
            ->update(['team_id' => null]);

        AuditLog::record(
            'deleted_team',
            'teams',
            "Deleted team: {$team->name}. Members and clients unassigned."
        );

        $team->delete();
    }

    /**
     * Assign member to team
     */
    public function assignMember(Team $team, User $user): void
    {
        $user->update(['team_id' => $team->id]);

        AuditLog::record(
            'assigned_member_to_team',
            'teams',
            "Assigned {$user->name} to team {$team->name}"
        );
    }

    /**
     * Remove member from team
     */
    public function removeMember(Team $team, User $user): void
    {
        $user->update(['team_id' => null]);

        AuditLog::record(
            'removed_member_from_team',
            'teams',
            "Removed {$user->name} from team {$team->name}"
        );
    }

    /**
     * Transfer all clients from one team to another
     */
    public function transferClients(Team $from, Team $to): int
    {
        $count = \App\Models\Client::where('team_id', $from->id)->count();

        \App\Models\Client::where('team_id', $from->id)
            ->update(['team_id' => $to->id]);

        AuditLog::record(
            'transferred_clients',
            'teams',
            "Transferred {$count} clients from {$from->name} to {$to->name}"
        );

        return $count;
    }

    /**
     * Generate unique team code
     */
    public function generateCode(): string
    {
        $last = Team::withTrashed()
            ->where('code', 'like', 'TEAM-%')
            ->orderByDesc('id')
            ->value('code');

        $lastNum = 0;
        if ($last) {
            $parts   = explode('-', $last);
            $lastNum = (int) end($parts);
        }

        do {
            $lastNum++;
            $code = 'TEAM-' . str_pad($lastNum, 3, '0', STR_PAD_LEFT);
        } while (Team::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    /**
     * Get all users not yet assigned to any team
     * for the assign member dropdown
     */
    public function getUnassignedUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereNull('team_id')
            ->whereDoesntHave('roles', fn($q) =>
                $q->where('name', 'super_admin')
            )
            ->get(['id', 'name', 'email', 'employee_code']);
    }

    /**
     * Get all active teams for dropdown
     */
    public function getActiveTeams(): \Illuminate\Database\Eloquent\Collection
    {
        return Team::where('is_active', true)->get(['id', 'name', 'code']);
    }
}