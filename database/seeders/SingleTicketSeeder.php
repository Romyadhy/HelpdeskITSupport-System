<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SingleTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single users that reporting the issue
        $reporter = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        // Create a single ticket
        Ticket::create([
            'user_id' => $reporter->id,
            'title' => 'Printer rusak dan tidak bisa ngeprint',
            'description' => 'Printer di ruang kantor tidak bisa ngeprint. Sudah coba restart printer tapi tetap tidak berfungsi.',
            'status' => 'Open',
            'priority' => 'Medium',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
