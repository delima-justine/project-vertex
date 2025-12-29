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
        Schema::create('tbl_progress', function (Blueprint $table) {
            $table->id('progress_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('tbl_user');
            $table->integer('required_hours')->nullable();
            $table->integer('logged_hours')->nullable();
            $table->enum('milestone', ['50%','75%','100%'])->nullable();
            $table->date('milestone_achieved_date')->nullable();
            $table->decimal('evaluation_score', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_progress');
    }
};
