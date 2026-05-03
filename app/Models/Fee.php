<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'title',
        'amount',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function assignToStudents(?Collection $students = null): void
    {
        $students ??= $this->classRoom->students()->get();

        foreach ($students as $student) {
            StudentFee::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'fee_id' => $this->id,
                ],
                [
                    'total_amount' => $this->amount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        }
    }
}
