<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['class_room_id', 'date'], 'attendances_unique_per_class_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
