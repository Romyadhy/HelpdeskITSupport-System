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
            $table->string('waiting_duration')->nullable();
            $table->string('progress_duration')->nullable();
            $table->string('total_duration')->nullable();
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
