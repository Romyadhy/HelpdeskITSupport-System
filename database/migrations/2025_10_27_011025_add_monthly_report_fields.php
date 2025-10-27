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
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->string('month')->after('user_id');
            $table->year('year')->after('month');

            $table->unsignedInteger('total_days_reported')->default(0)->after('report_date');
            $table->unsignedInteger('total_tasks')->default(0)->after('total_days_reported');
            $table->unsignedInteger('total_tickets')->default(0)->after('total_tasks');

            $table->json('daily_report_ids')->nullable()->after('content');
            $table->enum('status', ['Draft', 'Pending Verification', 'Verified'])
                  ->default('Draft')
                  ->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'month',
                'year',
                'total_days_reported',
                'total_tasks',
                'total_tickets',
                'daily_report_ids',
                'status',
            ]);
        });
    }
};
