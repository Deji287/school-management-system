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
         Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('numeric_value');
            $table->string('section', 10);
            $table->unsignedBigInteger('class_teacher_id')->nullable();
            $table->integer('capacity')->default(40);
            $table->string('academic_year');
            $table->timestamps();

            $table->foreign('class_teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
