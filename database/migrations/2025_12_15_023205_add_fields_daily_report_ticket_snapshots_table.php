<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_report_ticket_snapshots', function (Blueprint $table) {

            $table->unsignedBigInteger('created_by')->nullable()->after('ticket_id');
            $table->string('created_by_name')->nullable()->after('created_by');
            $table->unsignedBigInteger('category_id')->nullable()->after('created_by_name');
            $table->string('category_name')->nullable()->after('category_id');
            $table->unsignedBigInteger('location_id')->nullable()->after('category_name');
            $table->string('location_name')->nullable()->after('location_id');
            $table->timestamp('ticket_created_at')->nullable()->after('location_name');
            $table->timestamp('ticket_started_at')->nullable()->after('ticket_created_at');
            $table->timestamp('ticket_solved_at')->nullable()->after('ticket_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_report_ticket_snapshots', function (Blueprint $table) {
            //
        });
    }
};
