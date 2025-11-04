<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionReport extends Seeder
{
    public function run(): void
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Daily Reports ---
        Permission::firstOrCreate(['name' => 'create-daily-report']);
        Permission::firstOrCreate(['name' => 'view-daily-reports']);
        Permission::firstOrCreate(['name' => 'edit-daily-report']);    
        Permission::firstOrCreate(['name' => 'delete-daily-report']); 
        Permission::firstOrCreate(['name' => 'verify-daily-report']);
        
        // --- Monthly Reports ---
        Permission::firstOrCreate(['name' => 'create-monthly-report']);
        Permission::firstOrCreate(['name' => 'view-monthly-reports']);
        Permission::firstOrCreate(['name' => 'edit-monthly-report']);     
        Permission::firstOrCreate(['name' => 'delete-monthly-report']);    

 
        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);

        $supportRole->givePermissionTo([
            'create-daily-report',
            'view-daily-reports',
            'edit-daily-report', 
            'delete-daily-report',
        ]);

        $adminRole->givePermissionTo([
            'view-daily-reports',
            'delete-daily-report', 
            'verify-daily-report',
            'create-monthly-report',
            'view-monthly-reports',
            'edit-monthly-report',  
            'delete-monthly-report', 
        ]);

        $managerRole->givePermissionTo([
            'view-daily-reports',
            'view-monthly-reports',
        ]);
    }
}
