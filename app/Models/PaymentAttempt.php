<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id',
        'initiated_by_id',
        'provider',
        'provider_order_id',
        'provider_payment_id',
        'provider_signature',
        'receipt',
        'amount',
        'currency',
        'status',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by_id');
    }
}
