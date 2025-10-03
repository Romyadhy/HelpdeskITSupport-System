<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'support@gmail.com'], // email unik admin
            [
                'name' => 'IT Support',
                'password' => Hash::make('support123'), // ganti sesuai kebutuhan
                'role' => 'support', // set role sebagai support
            ]
        );
    }
}
