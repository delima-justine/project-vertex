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
        Schema::table('tbl_coordinator', function (Blueprint $table) {
            $table->string('unique_key', 50)->unique()->after('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_coordinator', function (Blueprint $table) {
            $table->dropColumn('unique_key');
        });
    }
};
