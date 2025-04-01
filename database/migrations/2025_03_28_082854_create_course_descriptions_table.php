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
        Schema::create('course_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_detail_id');
            $table->string('description');

            $table->timestamps();
            $table->foreign('course_detail_id')->references('id')->on('course_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_descriptions');
    }
};
