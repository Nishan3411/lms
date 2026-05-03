<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_room_id',
        'subject_id',
        'title',
        'description',
        'disk',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
    ];

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
}
