<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'location',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
