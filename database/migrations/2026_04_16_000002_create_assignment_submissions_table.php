<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->string('disk')->default('local');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->enum('status', ['submitted', 'graded'])->default('submitted');
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id'], 'assignment_submissions_unique_student');
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
