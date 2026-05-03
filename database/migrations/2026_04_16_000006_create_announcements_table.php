<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_room_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('audience', ['all', 'students', 'parents', 'teachers'])->default('all');
            $table->string('title');
            $table->text('body');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['audience', 'published_at']);
            $table->index(['class_room_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
