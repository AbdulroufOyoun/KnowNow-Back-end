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
        Schema::create('specialization_courses', function (Blueprint $table) {
            $table->id();
                        $table->unsignedBigInteger('course_id');
                        $table->unsignedBigInteger('specialization_id');
            $table->string('chapter');
            $table->integer('year');

            $table->timestamps();
                        $table->foreign('specialization_id')->references('id')->on('specializations');
                        $table->foreign('course_id')->references('id')->on('courses');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialization_courses');
    }
};