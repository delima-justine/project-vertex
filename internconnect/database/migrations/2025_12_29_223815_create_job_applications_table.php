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
        Schema::create('tbl_job_application', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('job_id')->nullable();
            $table->foreign('job_id')->references('job_id')->on('tbl_job_posting');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('tbl_user');
            $table->dateTime('application_date')->nullable();
            $table->string('resume_file_url', 255)->nullable();
            $table->string('cover_letter_file_url', 255)->nullable();
            $table->enum('hr_status', ['Pending Review','Interviewing','Hired','Rejected'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_job_application');
    }
};
