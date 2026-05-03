<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(Request $request): View
    {
        $scheduleEntries = $request->user()
            ->scheduleEntries()
            ->with(['classRoom', 'subject'])
            ->orderByRaw($this->dayOrderSql())
            ->orderBy('starts_at')
            ->get();

        return view('teacher.timetable', [
            'scheduleEntries' => $scheduleEntries,
        ]);
    }

    private function dayOrderSql(): string
    {
        return "CASE day_of_week WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7 ELSE 8 END";
    }
}
