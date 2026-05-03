<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'answer_text',
        'disk',
        'file_path',
        'original_filename',
        'status',
        'marks_obtained',
        'teacher_feedback',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
