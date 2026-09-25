<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'properties.approve', 'properties.feature', 'properties.manage-any',
            'requirements.view', 'requirements.create', 'requirements.update', 'requirements.delete',
            'requirements.respond', 'requirements.assign', 'requirements.manage-any',
            'enquiries.view', 'enquiries.manage',
            'categories.manage', 'amenities.manage', 'locations.manage',
            'users.manage', 'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => $permissions,
            'admin' => [
                'properties.view', 'properties.approve', 'properties.feature', 'properties.manage-any',
                'requirements.view', 'requirements.assign', 'requirements.manage-any',
                'enquiries.view', 'enquiries.manage',
                'categories.manage', 'amenities.manage', 'locations.manage',
            ],
            'owner' => [
                'properties.view', 'properties.create', 'properties.update', 'properties.delete',
                'requirements.view', 'requirements.respond',
                'enquiries.view',
            ],
            'agent' => [
                'properties.view', 'properties.create', 'properties.update', 'properties.delete',
                'requirements.view', 'requirements.respond',
                'enquiries.view',
            ],
            'developer' => [
                'properties.view', 'properties.create', 'properties.update', 'properties.delete',
                'requirements.view', 'requirements.respond',
                'enquiries.view',
            ],
            'buyer_tenant' => [
                'properties.view',
                'requirements.view', 'requirements.create', 'requirements.update', 'requirements.delete',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
