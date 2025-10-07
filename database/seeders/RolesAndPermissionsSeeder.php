<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create-ticket']);
        Permission::create(['name' => 'view-own-tickets']);
        Permission::create(['name' => 'view-any-tickets']);
        Permission::create(['name' => 'edit-own-ticket']);
        Permission::create(['name' => 'delete-own-ticket']);
        Permission::create(['name' => 'handle-ticket']);
        Permission::create(['name' => 'escalate-ticket']);
        Permission::create(['name' => 'handle-escalated-ticket']);
        Permission::create(['name' => 'close-ticket']);

        // create roles and assign permissions
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'create-ticket',
            'view-own-tickets',
            'edit-own-ticket',
            'delete-own-ticket',
        ]);

        $supportRole = Role::create(['name' => 'support']);
        $supportRole->givePermissionTo([
            'view-any-tickets',
            'handle-ticket',
            'escalate-ticket',
            'close-ticket',
        ]);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view-any-tickets',
            'handle-escalated-ticket',
            'close-ticket',
        ]);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo('view-any-tickets');
    }
}
