<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing data first (if any)
        DB::table('tbl_request')
            ->where('status', 'declined')
            ->update(['status' => DB::raw("'disapproved'")]);

        // 2. Change the ENUM definition
        DB::statement("ALTER TABLE tbl_request MODIFY COLUMN status ENUM('pending', 'approved', 'released', 'disapproved') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert existing data
        DB::table('tbl_request')
            ->where('status', 'disapproved')
            ->update(['status' => DB::raw("'declined'")]);

        // 2. Revert the ENUM definition
        DB::statement("ALTER TABLE tbl_request MODIFY COLUMN status ENUM('pending', 'approved', 'released', 'declined') DEFAULT 'pending'");
    }
};
