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
        Schema::create('tbl_job_posting', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('title', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('department', 50)->nullable();
            $table->string('salary_range', 50)->nullable();
            $table->unsignedBigInteger('posted_by_user_id')->nullable();
            $table->foreign('posted_by_user_id')->references('user_id')->on('tbl_user');
            $table->date('post_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_job_posting');
    }
};
