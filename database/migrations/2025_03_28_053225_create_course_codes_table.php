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
        Schema::create('course_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('code');

            $table->boolean('is_free')->default(false);
            $table->date('expire_at');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->softDeletes();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_codes');
    }
};
