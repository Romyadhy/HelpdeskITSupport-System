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
        Schema::create('daily_report_ticket_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');

            // Ticket data snapshot
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 50);
            $table->string('priority', 50)->nullable();
            $table->text('solution')->nullable();

            // Solver snapshot
            $table->unsignedBigInteger('solved_by')->nullable();
            $table->string('solved_by_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_ticket_snapshots');
    }
};
