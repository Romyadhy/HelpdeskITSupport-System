<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('handbooks', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('handbooks', function (Blueprint $table) {
             $table->dropColumn('file_path');
        });
    }
};
