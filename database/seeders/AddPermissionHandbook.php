<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionHandbook extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //Create Permission
        Permission::firstOrCreate(['name' => 'view-handbook']);
        Permission::firstOrCreate(['name' => 'create-handbook']);
        Permission::firstOrCreate(['name' => 'edit-handbook']);
        Permission::firstOrCreate(['name' => 'delete-handbook']);

        //Set to role
        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);

        $supportRole->givePermissionTo([
            'view-handbook',
        ]);
        $adminRole->givePermissionTo([
            'view-handbook',
            'create-handbook',
            'edit-handbook',
            'delete-handbook',

        ]);
        $managerRole->givePermissionTo([
            'view-handbook'
        ]);
    }
}
