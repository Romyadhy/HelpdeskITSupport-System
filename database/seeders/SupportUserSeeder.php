<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SupportUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'manager')->first();

        $user = User::create([
            'name' => 'Manager IT',
            'email' => 'managerit@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
        ]);
        $user->assignRole($role);
    }
}
