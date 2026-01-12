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
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->longText('about')->nullable()->after('contact_number');
            $table->longText('linkedin_url')->nullable()->after('about');
            $table->longText('github_url')->nullable()->after('linkedin_url');
            $table->longText('portfolio_url')->nullable()->after('github_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->dropColumn(['about', 'linkedin_url', 'github_url', 'portfolio_url']);
        });
    }
};
