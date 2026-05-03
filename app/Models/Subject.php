<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'name',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
