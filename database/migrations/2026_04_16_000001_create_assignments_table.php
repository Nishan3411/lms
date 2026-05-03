<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('max_marks')->default(100);
            $table->dateTime('due_at');
            $table->string('disk')->default('local');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->timestamps();

            $table->index(['teacher_id', 'class_room_id']);
            $table->index(['class_room_id', 'subject_id']);
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
