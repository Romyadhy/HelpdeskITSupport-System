<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketLookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ticket_categories')->insert([
            ['name' => 'Hardware'], 
            ['name' => 'Software'], 
            ['name' => 'Network'],
            ['name' => 'Other'],
        ]);

        DB::table('ticket_locations')->insert([
            ['name' => 'Ruang Bima'], 
            ['name' => 'Front Office'],
            ['name' => 'Ruang Kresna'],
            ['name' => 'Ruang Arjuna'],
            ['name' => 'Ruang Gatotkaca'],
            ['name' => 'Ruang Semar'],
            ['name' => 'Ruang Puntadewa'],
            ['name' => 'Ruang Yudhistira'],
            ['name' => 'Ruang Nakula'],
            ['name' => 'Ruang Sadewa'],
            ['name' => 'Ruang Abimanyu'],
            ['name' => 'Ruang Wisanggeni'],
            ['name' => 'Ruang Antareja'],
            ['name' => 'Other'],
           
            

        ]);
            }
}
