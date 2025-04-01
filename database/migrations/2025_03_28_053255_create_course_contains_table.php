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
        Schema::create('course_contains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('video');
            $table->text('pdf');
            $table->unsignedBigInteger('course_id');
            $table->boolean('is_free');
            $table->boolean('is_theoretical');

            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_contains');
    }
};
