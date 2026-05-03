<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('initiated_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_order_id')->unique();
            $table->string('provider_payment_id')->nullable();
            $table->string('provider_signature')->nullable();
            $table->string('receipt')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('created');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
