<?php

namespace Det\Members\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MemberPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            'member.view',
            'member.create',
            'member.edit',
            'member.delete',
            'member.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'member']);
        }

        // 2. Define Roles & Assign Permissions
        
        // Member Admin (All permissions)
        $adminRole = Role::firstOrCreate(['name' => 'member_admin', 'guard_name' => 'member']);
        $adminRole->syncPermissions($permissions);

        // Member Support (View & Edit)
        $supportRole = Role::firstOrCreate(['name' => 'member_support', 'guard_name' => 'member']);
        $supportRole->syncPermissions(['member.view', 'member.edit']);

        // Member (View only - usually for own profile, handled by logic)
        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'member']);
        $memberRole->syncPermissions(['member.view']);
    }
}