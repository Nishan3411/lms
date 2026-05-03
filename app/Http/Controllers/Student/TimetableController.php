<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(Request $request): View
    {
        $classIds = $request->user()->enrolledClasses()->pluck('class_rooms.id');

        $scheduleEntries = ScheduleEntry::query()
            ->with(['classRoom', 'subject', 'teacher'])
            ->whereIn('class_room_id', $classIds)
            ->orderByRaw($this->dayOrderSql())
            ->orderBy('starts_at')
            ->get();

        return view('student.timetable', [
            'scheduleEntries' => $scheduleEntries,
        ]);
    }

    private function dayOrderSql(): string
    {
        return "CASE day_of_week WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7 ELSE 8 END";
    }
}
