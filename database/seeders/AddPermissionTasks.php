<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionTasks extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create tasks permissions
        Permission::firstOrCreate(['name' => 'view-tasks']);
        Permission::firstOrCreate(['name' => 'create-task']);
        Permission::firstOrCreate(['name' => 'edit-task']);
        Permission::firstOrCreate(['name' => 'delete-task']);
        Permission::firstOrCreate(['name' => 'checked-task']);

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'view-tasks',
                'create-task',
                'edit-task',
                'delete-task',
            ]);
        }

        $supportRole = Role::where('name', 'support')->first();
        if ($supportRole) {
            $supportRole->givePermissionTo([
                'view-tasks',
                'checked-task',
            ]);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'view-tasks',
            ]);
        }

        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo('view-tasks');    
        }
    }
}
