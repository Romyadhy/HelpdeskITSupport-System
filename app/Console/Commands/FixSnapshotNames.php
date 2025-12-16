<?php

namespace App\Console\Commands;

use App\Models\DailyReportTicketSnapshot;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Illuminate\Console\Command;

class FixSnapshotNames extends Command
{
    protected $signature = 'snapshot:fix-names';

    protected $description = 'Backfill missing location_name and category_name in ticket snapshots';

    public function handle()
    {
        $this->info('Fixing missing snapshot names...');

        // Get all categories and locations for lookup
        $categories = TicketCategory::pluck('name', 'id');
        $locations = TicketLocation::pluck('name', 'id');

        // Find snapshots with missing names but have IDs
        $snapshots = DailyReportTicketSnapshot::where(function ($query) {
            $query->whereNull('category_name')
                ->whereNotNull('category_id');
        })->orWhere(function ($query) {
            $query->whereNull('location_name')
                ->whereNotNull('location_id');
        })->get();

        $fixed = 0;

        foreach ($snapshots as $snapshot) {
            $updated = false;

            // Fix category_name if missing
            if (empty($snapshot->category_name) && $snapshot->category_id) {
                $snapshot->category_name = $categories[$snapshot->category_id] ?? null;
                $updated = true;
            }

            // Fix location_name if missing
            if (empty($snapshot->location_name) && $snapshot->location_id) {
                $snapshot->location_name = $locations[$snapshot->location_id] ?? null;
                $updated = true;
            }

            if ($updated) {
                $snapshot->save();
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} snapshot(s) with missing names.");

        return Command::SUCCESS;
    }
}
