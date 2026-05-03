<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Support\StudentReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentReportController extends Controller
{
    public function index(Request $request, StudentReportBuilder $reportBuilder): View
    {
        $children = $request->user()
            ->children()
            ->with([
                'enrolledClasses' => fn ($query) => $query->with(['teachers', 'subjects'])->orderBy('name'),
                'attendanceRecords.attendance.classRoom',
                'attendanceRecords.attendance.subject',
                'examResults.exam.classRoom',
                'examResults.exam.subject',
                'assignmentSubmissions.assignment.classRoom',
                'assignmentSubmissions.assignment.subject',
            ])
            ->orderBy('name')
            ->get();

        $reports = $children->map(function ($child) use ($reportBuilder) {
            return [
                'student' => $child,
                'report' => $reportBuilder->build($child),
            ];
        });

        return view('parent.student-reports', [
            'reports' => $reports,
        ]);
    }
}
