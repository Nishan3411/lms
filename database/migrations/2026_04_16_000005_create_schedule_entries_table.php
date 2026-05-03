<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('location')->nullable();
            $table->timestamps();

            $table->unique(
                ['class_room_id', 'day_of_week', 'starts_at', 'ends_at'],
                'schedule_unique_class_slot'
            );
            $table->index(['teacher_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_entries');
    }
};
