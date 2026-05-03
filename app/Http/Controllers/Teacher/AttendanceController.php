<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAttendanceRequest;
use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classRooms = $teacher->teachingClasses()->with(['students', 'subjects'])->orderBy('name')->get();

        return view('teacher.attendance', [
            'classRooms' => $classRooms,
            'selectedClass' => null,
            'selectedSubject' => null,
            'selectedDate' => now()->toDateString(),
        ]);
    }

    public function markAttendance(Request $request): View
    {
        $teacher = $request->user();
        $classRooms = $teacher->teachingClasses()->with(['students', 'subjects'])->orderBy('name')->get();

        $selectedClass = $teacher->teachingClasses()
            ->with(['students', 'subjects'])
            ->findOrFail($request->integer('class_room_id'));

        $selectedSubject = $selectedClass->subjects()
            ->findOrFail($request->integer('subject_id'));

        return view('teacher.attendance', [
            'classRooms' => $classRooms,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
            'selectedDate' => $request->input('date', now()->toDateString()),
        ]);
    }

    public function storeAttendance(StoreAttendanceRequest $request): RedirectResponse
    {
        $classRoomId = $request->integer('class_room_id');
        $subjectId = $request->integer('subject_id');
        $date = $request->input('date');

        $exists = Attendance::where('class_room_id', $classRoomId)
            ->where('subject_id', $subjectId)
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('teacher.attendance.mark', [
                    'class_room_id' => $classRoomId,
                    'subject_id' => $subjectId,
                    'date' => $date,
                ])
                ->withErrors(['date' => 'Attendance for this subject and date has already been marked.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $classRoomId, $subjectId, $date): void {
            $attendance = Attendance::create([
                'class_room_id' => $classRoomId,
                'subject_id' => $subjectId,
                'date' => $date,
            ]);

            $attendance->records()->createMany($request->input('records'));
        });

        return redirect()
            ->route('teacher.attendance.index')
            ->with('success', 'Attendance marked successfully.');
    }
}
