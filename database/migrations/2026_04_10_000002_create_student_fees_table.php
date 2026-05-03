<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('fee_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->timestamps();

            $table->unique(['user_id', 'fee_id'], 'student_fees_unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
