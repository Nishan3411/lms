<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\ClassRoom;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function enrolledClasses()
    {
        return $this->belongsToMany(
            ClassRoom::class,
            'class_room_user',
            'user_id',
            'class_room_id'
        )->withTimestamps();
    }

    public function classRooms()
    {
        return $this->enrolledClasses();
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'student') {

                $compulsoryClasses = ClassRoom::where('type', 'compulsory')->pluck('id');

                $user->enrolledClasses()->syncWithoutDetaching($compulsoryClasses);

                if ($compulsoryClasses->isNotEmpty()) {
                    Fee::whereIn('class_room_id', $compulsoryClasses)->get()->each(
                        fn ($fee) => \App\Models\StudentFee::firstOrCreate(
                            [
                                'user_id' => $user->id,
                                'fee_id' => $fee->id,
                            ],
                            [
                                'total_amount' => $fee->amount,
                                'paid_amount' => 0,
                                'status' => 'pending',
                            ]
                        )
                    );
                }
            }
        });
    }

    public function assignOptionalClass($classRoomId)
    {
        $classRoom = ClassRoom::findOrFail($classRoomId);

        if ($classRoom->type !== 'optional') {
            throw new \Exception('This is not an optional class.');
        }

        // Check if already has optional
        $existingOptional = $this->enrolledClasses()
            ->where('type', 'optional')
            ->exists();

        if ($existingOptional) {
            throw new \Exception('Student already has an optional class.');
        }

        $this->enrolledClasses()->syncWithoutDetaching([$classRoomId]);

        $classRoom->fees->each(function ($fee) {
            \App\Models\StudentFee::firstOrCreate(
                [
                    'user_id' => $this->id,
                    'fee_id' => $fee->id,
                ],
                [
                    'total_amount' => $fee->amount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        });
    }

    public function children()
    {
        return $this->belongsToMany(
            User::class,
            'parent_student',
            'parent_id',
            'student_id'
        )->where('role', 'student')->withTimestamps();
    }

    public function parents()
    {
        return $this->belongsToMany(
            User::class,
            'parent_student',
            'student_id',
            'parent_id'
        )->where('role', 'parent')->withTimestamps();
    }

    public function teachingClasses()
    {
        return $this->belongsToMany(
            ClassRoom::class,
            'class_room_teacher',
            'teacher_id',
            'class_room_id'
        )->withTimestamps();
    }

    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class, 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'teacher_id');
    }

    public function scheduleEntries()
    {
        return $this->hasMany(ScheduleEntry::class, 'teacher_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function requestedLeaves()
    {
        return $this->hasMany(LeaveRequest::class, 'requester_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'student_id');
    }

    public function reviewedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'reviewed_by');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            StudentFee::class,
            'user_id',
            'student_fee_id',
            'id',
            'id'
        );
    }

    public function initiatedPaymentAttempts()
    {
        return $this->hasMany(PaymentAttempt::class, 'initiated_by_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }
}
