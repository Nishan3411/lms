<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user()->load([
            'children.enrolledClasses.subjects',
        ]);

        $children = $parent->children->map(function ($child) {
            return $child;
        });

        $classRooms = $children->flatMap->enrolledClasses->unique('id')->sortBy('name')->values();
        $subjects = $classRooms->flatMap->subjects->unique('id')->sortBy('name')->values();

        $children = $children->map(function ($child) use ($request) {
            $records = $child->attendanceRecords()
                ->with(['attendance.classRoom', 'attendance.subject'])
                ->whereHas('attendance', function ($query) use ($request): void {
                    $query
                        ->when($request->filled('class_room_id'), fn ($attendanceQuery) => $attendanceQuery->where('class_room_id', $request->integer('class_room_id')))
                        ->when($request->filled('subject_id'), fn ($attendanceQuery) => $attendanceQuery->where('subject_id', $request->integer('subject_id')))
                        ->when($request->filled('date'), fn ($attendanceQuery) => $attendanceQuery->whereDate('date', $request->date('date')))
                        ->when($request->filled('month'), fn ($attendanceQuery) => $attendanceQuery->whereYear('date', substr($request->input('month'), 0, 4))->whereMonth('date', substr($request->input('month'), 5, 2)));
                })
                ->get()
                ->sortByDesc(fn ($record) => $record->attendance->date)
                ->values();

            $child->setRelation('attendanceRecords', $records);
            $child->attendance_summary = $this->buildAttendanceSummary($records);
            $child->attendance_matrix = $this->buildAttendanceMatrix($records);

            return $child;
        });

        return view('parent.attendance', [
            'parent' => $parent,
            'children' => $children,
            'classRooms' => $classRooms,
            'subjects' => $subjects,
            'filters' => $request->only(['class_room_id', 'subject_id', 'date', 'month']),
        ]);
    }

    private function buildAttendanceSummary($records): array
    {
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $credited = $present + $late;
        $percentage = $total > 0 ? round(($credited / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'percentage' => $percentage,
        ];
    }

    private function buildAttendanceMatrix($records): array
    {
        $dates = $records
            ->map(fn ($record) => $record->attendance->date->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        $rows = $records
            ->groupBy(fn ($record) => $record->attendance->subject_id
                ? 'subject_'.$record->attendance->subject_id
                : 'class_'.$record->attendance->class_room_id)
            ->map(function ($subjectRecords) use ($dates) {
                $firstRecord = $subjectRecords->first();
                $statuses = [];

                foreach ($dates as $date) {
                    $statuses[$date] = optional(
                        $subjectRecords->first(fn ($record) => $record->attendance->date->format('Y-m-d') === $date)
                    )->status;
                }

                return [
                    'subject' => $firstRecord->attendance->subject?->name ?? $firstRecord->attendance->classRoom->name,
                    'class_room' => $firstRecord->attendance->classRoom->name,
                    'statuses' => $statuses,
                ];
            })
            ->sortBy('subject')
            ->values();

        return [
            'dates' => $dates,
            'rows' => $rows,
        ];
    }
}
