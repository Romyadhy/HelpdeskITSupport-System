<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        DailyReport::create([
            'user_id' => $user->id,
            'report_date' => now(),
            'content' => 'Laporan harian uji coba tanpa relasi task/ticket.',
        ]);
    }
}
