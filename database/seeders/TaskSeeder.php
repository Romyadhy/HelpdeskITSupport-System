<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::create([
            'title' => 'Review Infrastruktur',
            'description' => 'memeriksa kondisi perangkat keras (server, switch, router, UPS).',
            'frequency' => 'monthly',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
