<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the permission
        Permission::create(['name' => 'set-ticket-priority']);

        // Assign to admin role
        $admin = Role::findByName('admin');
        if ($admin) {
            $admin->givePermissionTo('set-ticket-priority');
        }

        // Assign to manager role (if exists)
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $manager->givePermissionTo('set-ticket-priority');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::findByName('set-ticket-priority');
        if ($permission) {
            $permission->delete();
        }
    }
};
