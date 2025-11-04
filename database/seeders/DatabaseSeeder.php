<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash as Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        // // admin
        // $admin = User::updateOrCreate(
        //     ['email' => 'admin2@gmail.com'],
        //     [
        //         'name' => 'Admin IT',
        //         'password' => Hash::make('admin123'),
        //         'role' => 'admin',
        //     ]
        // );
        // $admin->assignRole('admin');
        // // support
        // $support = User::updateOrCreate(
        //     ['email' => 'support2@gmail.com'],
        //     [
        //         'name' => 'IT Support',
        //         'password' => Hash::make('support123'),
        //         'role' => 'support',
        //     ]
        // );
        // $support->assignRole('support');
        // // manager
        // $manager = User::updateOrCreate(
        //     ['email' => 'manager@gmail.com'],
        //     [
        //         'name' => 'Manager IT',
        //         'password' => Hash::make('manager123'),
        //         'role' => 'manager',
        //     ]
        // );
        // $manager->assignRole('manager');
        // user
         $user = User::updateOrCreate(
            ['email' => 'user1@gmail.com'],
            [
                'name' => 'User1',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
        $user->assignRole('user');


    }
}
