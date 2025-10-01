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
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('solution')->nullable()->after('description');
            $table->foreignId('solved_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('solution');
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('solved_at')->nullable()->after('started_at');
            $table->integer('duration')->nullable()->after('solved_at'); // dalam menit/jam
            $table->string('category')->nullable()->after('priority');
            $table->string('location')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
             $table->dropColumn([
                'solution',
                'solved_by',
                'started_at',
                'solved_at',
                'duration',
                'category',
                'location',
            ]);
        });
    }
};
