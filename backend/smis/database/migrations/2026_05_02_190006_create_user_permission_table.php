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
        Schema::create('tbl_user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tbl_user')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('tbl_permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_user_permission');
    }
};
