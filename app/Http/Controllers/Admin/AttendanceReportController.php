<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $classRooms = ClassRoom::orderBy('name')->get();
        $subjects = Subject::query()
            ->when($request->filled('class_room_id'), fn ($query) => $query->where('class_room_id', $request->integer('class_room_id')))
            ->orderBy('name')
            ->get();

        $attendances = $this->attendanceQuery($request)->latest('date')->get();

        return view('admin.attendance-report', [
            'classRooms' => $classRooms,
            'subjects' => $subjects,
            'attendances' => $attendances,
            'filters' => $request->only(['class_room_id', 'subject_id', 'date', 'month']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $attendances = $this->attendanceQuery($request)->oldest('date')->get();

        return response()->streamDownload(function () use ($attendances): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Class', 'Subject', 'Student', 'Status']);

            foreach ($attendances as $attendance) {
                foreach ($attendance->records as $record) {
                    fputcsv($handle, [
                        $attendance->date->format('Y-m-d'),
                        $attendance->classRoom->name,
                        $attendance->subject?->name ?? 'General',
                        $record->student->name,
                        $record->status,
                    ]);
                }
            }

            fclose($handle);
        }, 'attendance-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function attendanceQuery(Request $request)
    {
        return Attendance::query()
            ->with(['classRoom', 'subject', 'records.student'])
            ->when($request->filled('class_room_id'), fn ($query) => $query->where('class_room_id', $request->integer('class_room_id')))
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('date', $request->date('date')))
            ->when($request->filled('month'), fn ($query) => $query->whereYear('date', substr($request->input('month'), 0, 4))->whereMonth('date', substr($request->input('month'), 5, 2)));
    }
}
