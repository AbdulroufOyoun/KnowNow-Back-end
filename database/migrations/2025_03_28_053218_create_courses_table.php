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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->text('poster');
            $table->unsignedBigInteger('university_id');
            $table->unsignedBigInteger('doctor_id');

            $table->timestamps();
            $table->foreign('doctor_id')->references('id')->on('users');

            $table->foreign('university_id')->references('id')->on('universities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
