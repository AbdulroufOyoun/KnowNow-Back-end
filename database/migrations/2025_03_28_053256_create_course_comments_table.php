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
        Schema::create('course_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('sub_comment')->nullable();
            $table->unsignedBigInteger('video_id');

            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('video_id')->references('id')->on('course_contains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_comments');
    }
};
