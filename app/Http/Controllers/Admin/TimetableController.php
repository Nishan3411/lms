<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleEntryRequest;
use App\Models\ClassRoom;
use App\Models\ScheduleEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(): View
    {
        return view('admin.timetable', [
            'classRooms' => ClassRoom::with(['subjects', 'teachers'])->orderBy('name')->get(),
            'teachers' => User::where('role', 'teacher')->orderBy('name')->get(),
            'scheduleEntries' => ScheduleEntry::with(['classRoom', 'subject', 'teacher'])
                ->orderByRaw($this->dayOrderSql())
                ->orderBy('starts_at')
                ->get(),
        ]);
    }

    public function store(StoreScheduleEntryRequest $request): RedirectResponse
    {
        ScheduleEntry::create($request->validated());

        return redirect()
            ->route('admin.timetable.index')
            ->with('success', 'Schedule entry added successfully.');
    }

    public function destroy(ScheduleEntry $scheduleEntry): RedirectResponse
    {
        $scheduleEntry->delete();

        return redirect()
            ->route('admin.timetable.index')
            ->with('success', 'Schedule entry removed successfully.');
    }

    private function dayOrderSql(): string
    {
        return "CASE day_of_week WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7 ELSE 8 END";
    }
}
