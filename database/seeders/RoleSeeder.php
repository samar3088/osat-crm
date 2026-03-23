<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ───────────────────────────────
        $permissions = [
            // Clients
            'view clients', 'create clients', 'edit clients', 'delete clients',

            // Team Members
            'view team_members', 'create team_members', 'edit team_members', 'delete team_members',

            // Conveyance
            'view conveyance', 'create conveyance', 'approve conveyance',

            // Targets
            'view targets', 'set targets',

            // Reports
            'view reports', 'export reports',

            // Settings
            'view settings', 'manage settings',

            // Teams
            'view teams', 'create teams', 'edit teams', 'delete teams',

            // Service Flags
            'view service_flags', 'create service_flags', 'resolve service_flags',

            // Audit
            'view audit_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Roles ─────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $subAdmin   = Role::firstOrCreate(['name' => 'sub_admin']);
        $opsAdmin   = Role::firstOrCreate(['name' => 'operations_admin']);
        $teamMember = Role::firstOrCreate(['name' => 'team_member']);
        $customer   = Role::firstOrCreate(['name' => 'customer']);

        // ── Super Admin — all permissions ─────────────
        $superAdmin->syncPermissions(Permission::all());

        // ── Sub Admin permissions ─────────────────────
        $subAdmin->syncPermissions([
            'view clients', 'create clients', 'edit clients',
            'view team_members', 'create team_members', 'edit team_members', 'delete team_members',
            'view conveyance', 'create conveyance', 'approve conveyance',
            'view targets', 'set targets',
            'view reports', 'export reports',
            'view teams',
            'view service_flags', 'create service_flags', 'resolve service_flags',
        ]);

        // ── Operations Admin permissions ──────────────
        $opsAdmin->syncPermissions([
            'view clients', 'edit clients',
            'view team_members',
            'view conveyance', 'create conveyance', 'approve conveyance',
            'view targets',
            'view reports',
            'view teams',
            'view service_flags', 'create service_flags', 'resolve service_flags',
        ]);

        // ── Team Member permissions ───────────────────
        $teamMember->syncPermissions([
            'view clients', 'create clients', 'edit clients',
            'view conveyance', 'create conveyance',
            'view targets',
            'view service_flags', 'create service_flags',
        ]);

        // ── Customer permissions ──────────────────────
        $customer->syncPermissions([]);
    }
}