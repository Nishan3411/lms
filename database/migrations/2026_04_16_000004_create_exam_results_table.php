<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id'], 'exam_results_unique_student');
            $table->index(['student_id', 'grade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
