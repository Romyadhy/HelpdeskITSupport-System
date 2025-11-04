<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class AddPermissionTakeOver extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create new permission
        Permission::create(['name' => 'take-over']);

        // assign the new permission to the support 
        $supportRole = Role::where('name', 'support')->first();
        if ($supportRole) {
            $supportRole->givePermissionTo('take-over');
        }
    }
}
