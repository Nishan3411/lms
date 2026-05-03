<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\LearningMaterial;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user()->load([
            'teachingClasses' => fn ($query) => $query
                ->withCount(['students', 'subjects'])
                ->with(['subjects.topics'])
                ->orderBy('name'),
        ]);

        $teachingClasses = $teacher->teachingClasses;
        $classRoomIds = $teachingClasses->pluck('id');
        $today = now()->format('l');
        $todayDate = now()->toDateString();

        $upcomingClasses = ScheduleEntry::with(['classRoom', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->orderBy('starts_at')
            ->get();

        $pendingAttendance = $teachingClasses->sum(function ($classRoom) use ($todayDate) {
            return $classRoom->subjects->filter(fn ($subject) => ! Attendance::where('class_room_id', $classRoom->id)
                ->where('subject_id', $subject->id)
                ->whereDate('date', $todayDate)
                ->exists())->count();
        });

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'teachingClasses' => $teachingClasses,
            'upcomingClasses' => $upcomingClasses,
            'stats' => [
                'classes' => $teachingClasses->count(),
                'students' => $teachingClasses->sum('students_count'),
                'subjects' => $teachingClasses->sum('subjects_count'),
                'topics' => $teachingClasses->sum(fn ($classRoom) => $classRoom->subjects->sum(fn ($subject) => $subject->topics->count())),
                'materials' => LearningMaterial::where('teacher_id', $teacher->id)->count(),
                'to_grade' => AssignmentSubmission::whereNull('marks_obtained')
                    ->whereHas('assignment', fn ($query) => $query->where('teacher_id', $teacher->id))
                    ->count(),
                'pending_attendance' => $pendingAttendance,
                'today_classes' => $upcomingClasses->count(),
            ],
        ]);
    }
}
