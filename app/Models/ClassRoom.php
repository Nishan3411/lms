<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'class_room_user',
            'class_room_id',
            'user_id'
        )->where('role', 'student')->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(
            User::class,
            'class_room_teacher',
            'class_room_id',
            'teacher_id'
        )->withTimestamps();
    }

    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function scheduleEntries()
    {
        return $this->hasMany(ScheduleEntry::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
