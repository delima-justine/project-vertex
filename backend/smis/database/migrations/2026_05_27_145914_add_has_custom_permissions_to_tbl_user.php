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
        Schema::table('tbl_user', function (Blueprint $row) {
            $row->boolean('has_custom_permissions')->default(false)->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user', function (Blueprint $row) {
            $row->dropColumn('has_custom_permissions');
        });
    }
};
