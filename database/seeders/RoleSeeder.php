<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Create Permissions ──────────────────────────

        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Customer management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'export customers',

            // Activity tracking
            'view activities',
            'create activities',
            'edit activities',
            'delete activities',

            // Conveyance
            'view conveyance',
            'create conveyance',
            'approve conveyance',

            // Reports
            'view reports',
            'export reports',

            // Settings
            'manage settings',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Create Roles & Assign Permissions ───────────

        // 1. Super Admin — gets ALL permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Team Member / RM — limited permissions
        $teamMember = Role::firstOrCreate(['name' => 'team_member']);
        $teamMember->syncPermissions([
            'view customers',
            'create customers',
            'edit customers',
            'view activities',
            'create activities',
            'edit activities',
            'view conveyance',
            'create conveyance',
            'view reports',
        ]);

        // 3. Customer — minimal permissions
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->syncPermissions([
            'view customers', // only own profile (scoped in controller)
            'view reports',   // only own reports (scoped in controller)
        ]);

        $this->command->info('✅ Roles and permissions seeded successfully!');
    }
}