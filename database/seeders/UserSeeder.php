<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'user')->first();

        $user = User::create([
            'name' => 'User4',
            'email' => 'user4@gmail.com',
            'password' => Hash::make('user4123'),
        ]);
        $user->assignRole($role);
    }
}
