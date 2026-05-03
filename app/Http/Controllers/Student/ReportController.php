<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Support\StudentReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request, StudentReportBuilder $reportBuilder): View
    {
        $student = $request->user();

        return view('student.report', [
            'student' => $student,
            'report' => $reportBuilder->build($student),
        ]);
    }
}
