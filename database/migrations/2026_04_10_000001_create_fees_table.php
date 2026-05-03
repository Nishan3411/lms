<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamps();

            $table->unique(['class_room_id', 'title', 'due_date'], 'fees_unique_per_class_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
