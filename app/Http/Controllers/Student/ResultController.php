<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->load([
            'examResults.exam.classRoom',
            'examResults.exam.subject',
            'examResults.exam.teacher',
        ]);

        $results = $student->examResults
            ->filter(fn ($result) => $result->exam->isPublished())
            ->sortByDesc(fn ($result) => $result->exam->exam_date)
            ->values();

        return view('student.results', [
            'student' => $student,
            'results' => $results,
            'summary' => $this->summary($results),
        ]);
    }

    private function summary($results): array
    {
        $totalMarks = $results->sum(fn ($result) => (float) $result->marks_obtained);
        $maxMarks = $results->sum(fn ($result) => (float) $result->exam->max_marks);

        return [
            'count' => $results->count(),
            'percentage' => $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 2) : 0,
        ];
    }
}
