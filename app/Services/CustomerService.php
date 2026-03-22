<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\AuditLog;

class CustomerService 
{
    /**
     * Get all customers — scoped by role
     */
    public function getAll(string $search = ''): \Illuminate\Support\Collection
    {
        $user  = auth()->user();
        $query = Client::with(['assignedTo', 'createdBy'])
            ->latest();

        // ── Data Scoping ────────────────────────────
        if (!$user->isSuperAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('client_name',   'like', "%{$search}%")
                  ->orWhere('client_pan',   'like', "%{$search}%")
                  ->orWhere('client_mobile','like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(fn($c) => [
            'id'            => $c->id,
            'client_name'   => $c->client_name,
            'client_pan'    => $c->client_pan   ?? '—',
            'client_mobile' => $c->client_mobile ?? '—',
            'client_email'  => $c->client_email  ?? '—',
            'client_type'   => $c->client_type   ?? '—',
            'source_detail' => $c->source_detail ?? '—',
            'assigned_to'   => $c->assignedTo?->name ?? '—',
            'assigned_to_id'=> $c->assigned_to,
            'is_active'     => $c->is_active,
            'follow_date'   => $c->follow_date?->format('d M Y') ?? '—',
            'created_at'    => $c->created_at->format('d M Y'),
        ]);
    }

    /**
     * Create customer
     */
    public function create(array $data): Client
    {
        $user   = auth()->user();
        $client = Client::create([
            'client_name'    => $data['client_name'],
            'client_pan'     => $data['client_pan']     ?? null,
            'client_mobile'  => $data['client_mobile']  ?? null,
            'client_email'   => $data['client_email']   ?? null,
            'client_type'    => $data['client_type']    ?? null,
            'source_detail'  => $data['source_detail']  ?? null,
            'date_of_birth'  => $data['date_of_birth']  ?? null,
            'follow_date'    => $data['follow_date']    ?? null,
            'full_remarks'   => $data['full_remarks']   ?? null,
            'latest_remarks' => $data['full_remarks']   ?? null,
            'assigned_to'    => $data['assigned_to']    ?? ($user->isTeamMember() ? $user->id : null),
            'created_by'     => $user->id,
            'is_active'      => true,
            'date_first_added' => now()->toDateString(),
        ]);

        AuditLog::record(
            'created_client',
            'customers',
            "Created client: {$client->client_name}"
        );

        return $client;
    }

    /**
     * Update customer
     */
    public function update(Client $client, array $data): Client
    {
        $client->update([
            'client_name'    => $data['client_name'],
            'client_pan'     => $data['client_pan']     ?? null,
            'client_mobile'  => $data['client_mobile']  ?? null,
            'client_email'   => $data['client_email']   ?? null,
            'client_type'    => $data['client_type']    ?? null,
            'source_detail'  => $data['source_detail']  ?? null,
            'date_of_birth'  => $data['date_of_birth']  ?? null,
            'follow_date'    => $data['follow_date']    ?? null,
            'full_remarks'   => $data['full_remarks']   ?? null,
            'latest_remarks' => $data['full_remarks']   ?? null,
            'assigned_to'    => $data['assigned_to']    ?? null,
            'is_active'      => $data['is_active']      ?? $client->is_active,
        ]);

        AuditLog::record(
            'updated_client',
            'customers',
            "Updated client: {$client->client_name}"
        );

        return $client->fresh();
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Client $client): Client
    {
        $client->update(['is_active' => !$client->is_active]);

        AuditLog::record(
            'toggled_client_status',
            'customers',
            "Changed status of {$client->client_name}"
        );

        return $client->fresh();
    }

    /**
     * Soft delete
     */
    public function delete(Client $client): void
    {
        AuditLog::record(
            'deleted_client',
            'customers',
            "Deleted client: {$client->client_name}"
        );

        $client->delete();
    }

    /**
     * Get team members for assign dropdown
     * Scoped — RM only sees themselves
     */
    public function getTeamMembers(): \Illuminate\Database\Eloquent\Collection
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return User::role('team_member')
                ->where('is_active', true)
                ->get(['id', 'name', 'employee_code']);
        }

        // RM can only assign to themselves
        return User::where('id', $user->id)->get(['id', 'name', 'employee_code']);
    }
}