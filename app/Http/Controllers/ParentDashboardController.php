<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user()->load([
            'children' => fn ($query) => $query
                ->with(['attendanceRecords', 'fees.fee', 'examResults.exam', 'enrolledClasses.teachers', 'enrolledClasses.subjects.topics'])
                ->orderBy('name'),
        ]);

        $children = $parent->children;
        $children = $children->map(function ($child) {
            $attendanceTotal = $child->attendanceRecords->count();
            $attendanceCredited = $child->attendanceRecords->whereIn('status', ['present', 'late'])->count();
            $child->attendance_percentage = $attendanceTotal > 0 ? round(($attendanceCredited / $attendanceTotal) * 100, 2) : 0;
            $child->pending_dues = $child->fees->whereIn('status', ['pending', 'partial'])->sum(fn ($studentFee) => $studentFee->pendingAmount());
            $publishedResults = $child->examResults->filter(fn ($result) => $result->exam?->published_at);
            $child->result_average = $publishedResults->isNotEmpty()
                ? round($publishedResults->avg(fn ($result) => ((float) $result->marks_obtained / max(1, $result->exam->max_marks)) * 100), 2)
                : null;
            $child->latest_materials = LearningMaterial::with(['classRoom', 'subject', 'teacher'])
                ->whereIn('class_room_id', $child->enrolledClasses->pluck('id'))
                ->latest()
                ->take(3)
                ->get();

            return $child;
        });

        return view('parent.dashboard', [
            'parent' => $parent,
            'children' => $children,
            'stats' => [
                'children' => $children->count(),
                'classes' => $children->sum(fn ($child) => $child->enrolledClasses->count()),
                'subjects' => $children->sum(fn ($child) => $child->enrolledClasses->sum(fn ($classRoom) => $classRoom->subjects->count())),
                'topics' => $children->sum(fn ($child) => $child->enrolledClasses->sum(fn ($classRoom) => $classRoom->subjects->sum(fn ($subject) => $subject->topics->count()))),
            ],
        ]);
    }
}
