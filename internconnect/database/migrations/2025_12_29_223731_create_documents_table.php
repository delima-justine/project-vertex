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
        Schema::create('tbl_document', function (Blueprint $table) {
            $table->id('doc_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('tbl_user');
            $table->enum('doc_type', ['MOA','Endorsement Letter','Certificate of Completion','Clearance'])->nullable();
            $table->date('submission_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('verification_status', ['Pending','Verified','Rejected'])->nullable();
            $table->string('file_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_document');
    }
};
