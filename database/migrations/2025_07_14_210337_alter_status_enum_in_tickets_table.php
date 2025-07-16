<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('open', 'closed') DEFAULT 'open'");
        });
    }
};
