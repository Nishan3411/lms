<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->with([
                'examResults.exam.classRoom',
                'examResults.exam.subject',
                'examResults.exam.teacher',
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($child) {
                $results = $child->examResults
                    ->filter(fn ($result) => $result->exam->isPublished())
                    ->sortByDesc(fn ($result) => $result->exam->exam_date)
                    ->values();

                $child->setRelation('examResults', $results);
                $child->result_summary = $this->summary($results);

                return $child;
            });

        return view('parent.results', [
            'children' => $children,
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
