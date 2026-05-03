<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\LearningMaterial;
use App\Models\ScheduleEntry;
use App\Support\StudentReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request, StudentReportBuilder $reportBuilder): View
    {
        $student = $request->user()->load([
            'enrolledClasses' => fn ($query) => $query
                ->with(['teachers', 'subjects.topics'])
                ->orderBy('name'),
            'parents',
        ]);

        $enrolledClasses = $student->enrolledClasses;
        $classRoomIds = $enrolledClasses->pluck('id');
        $submittedAssignmentIds = $student->assignmentSubmissions()->pluck('assignment_id');
        $attendanceRecords = $student->attendanceRecords;
        $attendanceTotal = $attendanceRecords->count();
        $attendanceCredited = $attendanceRecords->whereIn('status', ['present', 'late'])->count();
        $attendancePercentage = $attendanceTotal > 0 ? round(($attendanceCredited / $attendanceTotal) * 100, 2) : 0;
        $todaySchedule = ScheduleEntry::with(['classRoom', 'subject', 'teacher'])
            ->whereIn('class_room_id', $classRoomIds)
            ->where('day_of_week', now()->format('l'))
            ->orderBy('starts_at')
            ->get();
        $dueAssignments = Assignment::with(['classRoom', 'subject'])
            ->whereIn('class_room_id', $classRoomIds)
            ->whereNotIn('id', $submittedAssignmentIds)
            ->where('due_at', '>=', now())
            ->orderBy('due_at')
            ->take(5)
            ->get();
        $latestMaterials = LearningMaterial::with(['classRoom', 'subject', 'teacher'])
            ->whereIn('class_room_id', $classRoomIds)
            ->latest()
            ->take(5)
            ->get();
        $pendingFees = $student->fees()->with('fee')->whereIn('status', ['pending', 'partial'])->get();
        $report = $reportBuilder->build($student);

        return view('student.dashboard', [
            'student' => $student,
            'enrolledClasses' => $enrolledClasses,
            'todaySchedule' => $todaySchedule,
            'dueAssignments' => $dueAssignments,
            'latestMaterials' => $latestMaterials,
            'pendingFees' => $pendingFees,
            'attendancePercentage' => $attendancePercentage,
            'report' => $report,
            'stats' => [
                'classes' => $enrolledClasses->count(),
                'subjects' => $enrolledClasses->sum(fn ($classRoom) => $classRoom->subjects->count()),
                'topics' => $enrolledClasses->sum(fn ($classRoom) => $classRoom->subjects->sum(fn ($subject) => $subject->topics->count())),
                'parents' => $student->parents->count(),
                'today_classes' => $todaySchedule->count(),
                'due_assignments' => $dueAssignments->count(),
                'materials' => $latestMaterials->count(),
                'pending_dues' => $pendingFees->sum(fn ($studentFee) => $studentFee->pendingAmount()),
            ],
        ]);
    }
}
