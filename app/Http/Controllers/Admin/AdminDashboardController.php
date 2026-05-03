<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'classes' => ClassRoom::count(),
            'subjects' => Subject::count(),
            'topics' => Topic::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'parents' => User::where('role', 'parent')->count(),
            'total_dues' => StudentFee::whereIn('status', ['pending', 'partial'])->get()->sum(fn ($studentFee) => $studentFee->pendingAmount()),
            'overdue_fees' => StudentFee::whereIn('status', ['pending', 'partial'])
                ->whereHas('fee', fn ($query) => $query->whereDate('due_date', '<', now()->toDateString()))
                ->count(),
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'attendance_percentage' => $this->attendancePercentage(),
        ];

        $classSummaries = ClassRoom::query()
            ->withCount(['subjects', 'students', 'teachers'])
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'classSummaries' => $classSummaries,
        ]);
    }

    private function attendancePercentage(): float
    {
        $total = AttendanceRecord::count();

        if ($total === 0) {
            return 0;
        }

        $credited = AttendanceRecord::whereIn('status', ['present', 'late'])->count();

        return round(($credited / $total) * 100, 2);
    }
}
