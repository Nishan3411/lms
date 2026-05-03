<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fee_id',
        'total_amount',
        'paid_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentAttempts()
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function pendingAmount(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function syncStatus(): void
    {
        $paid = (float) $this->paid_amount;
        $total = (float) $this->total_amount;

        $this->status = match (true) {
            $paid <= 0 => 'pending',
            $paid >= $total => 'paid',
            default => 'partial',
        };
    }
}
