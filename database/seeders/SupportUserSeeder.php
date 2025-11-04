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
        $role = Role::where('name', 'support')->first();
        
        $user = User::create([
            'name' => 'Support Agent 4',
            'email' => 'support4@gmail.com',
            'password' => Hash::make('support123'),
            'role' => 'support',
        ]);
        $user->assignRole($role);
    }
}
