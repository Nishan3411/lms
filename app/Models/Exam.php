<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_room_id',
        'subject_id',
        'title',
        'exam_date',
        'max_marks',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'max_marks' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
