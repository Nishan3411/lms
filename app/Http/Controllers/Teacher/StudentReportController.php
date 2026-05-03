<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\StudentReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentReportController extends Controller
{
    public function index(Request $request, StudentReportBuilder $reportBuilder): View
    {
        $teacher = $request->user();
        $classRooms = $teacher->teachingClasses()
            ->with(['students' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $filteredClassRooms = $request->filled('class_room_id')
            ? $classRooms->where('id', $request->integer('class_room_id'))->values()
            : $classRooms;

        $students = $filteredClassRooms
            ->flatMap->students
            ->unique('id')
            ->sortBy('name')
            ->values();

        $selectedStudent = null;

        if ($request->filled('student_id')) {
            $selectedStudent = $students->firstWhere('id', $request->integer('student_id'));
            abort_unless($selectedStudent instanceof User, 403);
        } elseif ($students->isNotEmpty()) {
            $selectedStudent = $students->first();
        }

        return view('teacher.student-reports', [
            'classRooms' => $classRooms,
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedClassRoomId' => $request->input('class_room_id'),
            'report' => $selectedStudent ? $reportBuilder->build($selectedStudent) : null,
        ]);
    }
}
