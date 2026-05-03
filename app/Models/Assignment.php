<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_room_id',
        'subject_id',
        'title',
        'description',
        'max_marks',
        'due_at',
        'disk',
        'attachment_path',
        'attachment_original_name',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'max_marks' => 'integer',
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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
