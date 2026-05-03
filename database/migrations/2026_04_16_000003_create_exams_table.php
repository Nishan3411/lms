<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('exam_date');
            $table->unsignedInteger('max_marks')->default(100);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['class_room_id', 'subject_id', 'title', 'exam_date'], 'exams_unique_schedule');
            $table->index(['teacher_id', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
